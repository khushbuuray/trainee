<?php

declare(strict_types=1);

namespace ICTECHDataCleanerPro\Service\Cleanup;

use DateTime;
use RecursiveArrayIterator;
use RecursiveIteratorIterator;
use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\RangeFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\NotFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\MultiFilter;
use Shopware\Core\Content\Media\MediaEntity;
use Shopware\Core\Content\Media\MediaCollection;
use ICTECHDataCleanerPro\Service\CleanupLoggerService;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;

class MediaCleanupHandler implements CleanupHandlerInterface
{
    /** @var EntityRepository<MediaCollection> */
    private readonly EntityRepository $mediaRepository;

    /** @var EntityRepository<EntityCollection<Entity>> */
    private readonly EntityRepository $themeRepository;

    private readonly CleanupLoggerService $logger;
    private readonly Connection $connection;

    /**
     * @param EntityRepository<MediaCollection> $mediaRepository
     * @param EntityRepository<EntityCollection<Entity>> $themeRepository
     */
    public function __construct(
        EntityRepository $mediaRepository,
        EntityRepository $themeRepository,
        CleanupLoggerService $logger,
        Connection $connection
    ) {
        $this->mediaRepository = $mediaRepository;
        $this->themeRepository = $themeRepository;
        $this->logger = $logger;
        $this->connection = $connection;
    }

    /**
     * @param array<string, mixed> $config
     * @return array{name: string, items: array<string, array{count: int, sample: list<array{id: string, name: string, created_at?: string|null}>}>}
     */
    public function cleanup(array $config, bool $dryRun, Context $context): array
    {
        $daysRaw = $config['mediaCleanup.orphanAgeDays'] ?? null;
        $days = is_numeric($daysRaw) ? (int) $daysRaw : 30;

        return [
            'name' => $this->getName(),
            'items' => [
                'orphaned_media' => $this->cleanupOrphanedMedia($days, $dryRun, $context),
            ],
        ];
    }

    /**
     * @return array{count: int, sample: list<array{id: string, name: string, created_at?: string|null}>}
     */
    private function cleanupOrphanedMedia(int $days, bool $dryRun, Context $context): array
    {
        $cutoff = (new DateTime())->modify("-{$days} days");
        // Step 1: Get theme media IDs
        $themeMediaIds = [];

        /** @var EntityCollection<Entity> $themes */
        $themes = $this->themeRepository->search(new Criteria(), $context)->getEntities();

        foreach ($themes as $theme) {
            /** @var array<string, mixed> $configValues */
            $configValues = $theme->get('configValues') ?? [];

            $flat = new RecursiveIteratorIterator(
                new RecursiveArrayIterator($configValues),
                RecursiveIteratorIterator::SELF_FIRST
            );

            foreach ($flat as $value) {
                if (is_array($value) && isset($value['value']) && is_string($value['value']) && Uuid::isValid($value['value'])) {
                    $themeMediaIds[] = strtolower($value['value']);
                } elseif (is_string($value) && Uuid::isValid($value)) {
                    $themeMediaIds[] = strtolower($value);
                }
            }
        }

        // Step 2: Get product cover media IDs
        $coverMediaIds = array_map(
            static fn(mixed $id): string => is_string($id) ? strtolower($id) : '',
            array_filter($this->connection->fetchFirstColumn("SELECT LOWER(HEX(cover)) FROM product WHERE cover IS NOT NULL"))
        );

        // Step 3: Get theme_media media IDs
        $themeMediaExtraIds = array_map(
            static fn(mixed $id): string => is_string($id) ? strtolower($id) : '',
            array_filter($this->connection->fetchFirstColumn("SELECT LOWER(HEX(media_id)) FROM theme_media WHERE media_id IS NOT NULL"))
        );

        // Step 4: Merge all exclusions
        $excludeMediaIds = array_unique(array_merge($coverMediaIds, $themeMediaIds, $themeMediaExtraIds));

        // 🔽 STEP 5: Exclude media used in product_download
        $productDownloadMediaIds = array_map(
            static fn(mixed $id): string => is_string($id) ? strtolower($id) : '',
            array_filter($this->connection->fetchFirstColumn("SELECT LOWER(HEX(media_id)) FROM product_download WHERE media_id IS NOT NULL"))
        );

        // 🔽 STEP 6: Exclude media used in order_line_item_download
        $orderLineDownloadMediaIds = array_map(
            static fn(mixed $id): string => is_string($id) ? strtolower($id) : '',
            array_filter($this->connection->fetchFirstColumn("SELECT LOWER(HEX(media_id)) FROM order_line_item_download WHERE media_id IS NOT NULL"))
        );

        // Step 7: Merge all exclusions into a single array
        $excludeMediaIds = array_unique(array_merge(
            $excludeMediaIds,
            $productDownloadMediaIds,
            $orderLineDownloadMediaIds
        ));
        // Step 5: Query orphaned media
        $criteria = new Criteria();
        $criteria->addFilter(new RangeFilter('createdAt', [RangeFilter::LT => $cutoff->format(DATE_ATOM)]));
        $filters = [];

        if (!empty($excludeMediaIds)) {
            $filters[] = new NotFilter(MultiFilter::CONNECTION_AND, [
                new EqualsAnyFilter('id', $excludeMediaIds),
            ]);
        }

        // ✅ Explicitly exclude media linked to documents
        $filters[] = new EqualsFilter('documents.id', null);
        $criteria->addFilter(new MultiFilter(MultiFilter::CONNECTION_AND, $filters));



        $criteria->addAssociations([
            'productMedia',
            'productManufacturers',
            'categories',
            'cmsBlocks',
            'cmsSections',
            'cmsPages',
            'documents',
            'themes',
        ]);
        if ($dryRun) {
        $criteria->setLimit(1000);
        }
        // $criteria->setLimit(3000);

        /** @var MediaCollection $media */
        $media = $this->mediaRepository->search($criteria, $context)->getEntities();

        /** @var list<array{id: string, name: string, created_at?: string|null}> $sample */
        $sample = [];
        $idsToDelete = [];

        foreach ($media as $mediaEntity) {
            $sample[] = [
                'id' => $mediaEntity->getId(),
                'name' => $mediaEntity->getFileName() ?? '',
                'created_at' => $mediaEntity->getCreatedAt()?->format('Y-m-d H:i:s') ?: null,
            ];
            $idsToDelete[] = ['id' => $mediaEntity->getId()];
        }

        $this->logger->logToFile('media', 'info', [
            'function' => 'cleanupOrphanedMedia',
            'cutoff_date' => $cutoff->format(DATE_ATOM),
            'found_count' => count($idsToDelete),
            'excluded_ids' => $excludeMediaIds,
        ]);
        if (!$dryRun && $idsToDelete !== []) {
            try {
                $this->mediaRepository->delete($idsToDelete, $context);
            } catch (\Throwable $e) {
                $this->logger->logError('media', $e, [
                    'action' => 'delete_orphaned',
                    'ids' => array_column($idsToDelete, 'id'),
                ]);
                throw $e;
            }
        }

        return [
            'count' => count($idsToDelete),
            'sample' => $sample,
        ];
    }
    public function getName(): string
    {
        return 'Media Cleanup';
    }

    public function getKey(): string
    {
        return 'mediaCleanup';
    }
}
