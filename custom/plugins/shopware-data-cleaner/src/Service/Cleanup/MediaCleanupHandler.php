<?php declare(strict_types=1);

namespace IctDataCleanerPro\Service\Cleanup;

use DateTime;
 
use Throwable;
 
use RecursiveIteratorIterator;
 
use RecursiveArrayIterator;
 
use Doctrine\DBAL\Connection;
 
use Shopware\Core\Framework\Context;
 
use Shopware\Core\Framework\Uuid\Uuid;
 
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
 
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
 
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\RangeFilter;
 
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\NotFilter;
 
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
 
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\MultiFilter;
 
use IctDataCleanerPro\Service\CleanupLoggerService;

class MediaCleanupHandler implements CleanupHandlerInterface
 
{
 
    public function __construct(
 
        private readonly EntityRepository $mediaRepository,
 
        private readonly EntityRepository $themeRepository,
 
        private readonly CleanupLoggerService $logger,
 
        private readonly Connection $connection
 
    ) {}

    public function cleanup(array $config, bool $dryRun, Context $context): array
 
    {
 
        return [
 
            'name' => $this->getName(),
 
            'items' => [
 
                'orphaned_media' => $this->cleanupOrphanedMedia((int) $config['mediaCleanup.orphanAgeDays'], $dryRun, $context)
 
            ]
 
        ];
 
    }

private function cleanupOrphanedMedia(int $days, bool $dryRun, Context $context): array

{

    $cutoff = (new \DateTime())->modify("-{$days} days");
 
    // Step 1: Collect theme config media IDs

    $themeMediaIds = [];

    $themes = $this->themeRepository->search(new Criteria(), $context);

    foreach ($themes as $theme) {

        $configValues = $theme->getConfigValues() ?? [];
 
        $flat = new \RecursiveIteratorIterator(

            new \RecursiveArrayIterator($configValues),

            \RecursiveIteratorIterator::SELF_FIRST

        );
 
        foreach ($flat as $value) {

            if (is_array($value) && isset($value['value']) && is_string($value['value']) && Uuid::isValid($value['value'])) {

                $themeMediaIds[] = $value['value'];

            } elseif (is_string($value) && Uuid::isValid($value)) {

                $themeMediaIds[] = $value;

            }

        }
 
    }

    $themeMediaIds = array_unique($themeMediaIds);
 
    // Step 2: Collect product cover media IDs

    $coverMediaIds = $this->connection->fetchFirstColumn("SELECT LOWER(HEX(cover)) FROM product WHERE cover IS NOT NULL");

    $coverMediaIds = array_filter(array_map('strtolower', $coverMediaIds));
 
    // Step 3: Find media used in theme_media

    $themeMediaExtraIds = $this->connection->fetchFirstColumn("SELECT LOWER(HEX(media_id)) FROM theme_media WHERE media_id IS NOT NULL");

    $themeMediaExtraIds = array_filter(array_map('strtolower', $themeMediaExtraIds));
 
    // Merge all exclusion IDs

    $excludeMediaIds = array_unique(array_merge($coverMediaIds, $themeMediaIds, $themeMediaExtraIds));
 
    // Step 4: Build criteria

    $criteria = new Criteria();

    $criteria->addFilter(new RangeFilter('createdAt', [RangeFilter::LT => $cutoff->format(DATE_ATOM)]));
 
    if (!empty($excludeMediaIds)) {

        $criteria->addFilter(new NotFilter(NotFilter::CONNECTION_AND, [

            new EqualsAnyFilter('id', $excludeMediaIds),

        ]));

    }
 
    // Add associations

    $criteria->addAssociations([

        'productMedia',

        'productManufacturers',

        'categories',

        'cmsBlocks',

        'cmsSections',

        'cmsPages',

        'documents',

        // 'mediaThumbnails',

        'themes',

    ]);
 
    $criteria->setLimit(1000);
 
    // Step 5: Search orphaned media

    $result = $this->mediaRepository->search($criteria, $context);

    $media = $result->getEntities();
 
    $sample = [];

    $idsToDelete = [];

    foreach ($media as $mediaEntity) {

        $sample[] = [

            'id' => $mediaEntity->getId(),

            'file_name' => $mediaEntity->getFileName(),

            'created_at' => $mediaEntity->getCreatedAt()?->format('Y-m-d H:i:s'),

        ];

        $idsToDelete[] = ['id' => $mediaEntity->getId()];

    }
 
    $this->logger->logToFile('media', 'info', [

        'function' => 'cleanupOrphanedMedia',

        'cutoff_date' => $cutoff->format(DATE_ATOM),

        'found_count' => count($idsToDelete),

        'excluded_ids' => $excludeMediaIds,

    ]);
 
    if (!$dryRun && count($idsToDelete) > 0) {

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
 
}
 
 