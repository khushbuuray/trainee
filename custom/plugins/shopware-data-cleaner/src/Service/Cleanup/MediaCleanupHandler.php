<?php declare(strict_types=1);

namespace IctDataCleanerPro\Service\Cleanup;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\RangeFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\MultiFilter;

class MediaCleanupHandler implements CleanupHandlerInterface
{
    private EntityRepository $mediaRepository;
    private Connection $connection;

    public function __construct(
        EntityRepository $mediaRepository,
        Connection $connection
    ) {
        $this->mediaRepository = $mediaRepository;
        $this->connection = $connection;
    }

    public function cleanup(array $config, bool $dryRun, Context $context): array
    {
        $results = [
            'name' => $this->getName(),
            'items' => []
        ];

        if (isset($config['mediaCleanup.orphanAgeDays'])) {
            $orphanResults = $this->cleanupOrphanedMedia((int) $config['mediaCleanup.orphanAgeDays'], $dryRun, $context);
            $results['items']['orphaned_media'] = $orphanResults;
        }

    
        return $results;
    }


    private function cleanupOrphanedMedia(int $days, bool $dryRun, Context $context): array
    {
        $date = (new \DateTime())->modify("-{$days} days");

        $criteria = new Criteria();
        $criteria->addFilter(
            new RangeFilter('createdAt', [RangeFilter::LT => $date->format(DATE_ATOM)])
        );
        $criteria->addFilter(new MultiFilter(MultiFilter::CONNECTION_AND, [
            new EqualsFilter('productMedia.id', null),
            new EqualsFilter('categories.id', null),
            new EqualsFilter('cmsPages.id', null),
            new EqualsFilter('cmsBlocks.id', null),
            new EqualsFilter('productManufacturers.id', null),
            new EqualsFilter('documents.id', null),
            new EqualsFilter('themes.id', null),
        ]));
        $criteria->addAssociations([
            'productMedia',
            'categories',
            'cmsPages',
            'cmsBlocks',
            'productManufacturers',
            'documents',
            'themes',
        ]);
        $criteria->setLimit(1000); // load all now; or use pagination if supported

        $result = $this->mediaRepository->search($criteria, $context);
        $media = $result->getEntities();

        $sample = [];
        foreach ($media as $mediaEntity) {
            $sample[] = [
                'id' => $mediaEntity->getId(),
                'file_name' => $mediaEntity->getFileName(),
                'created_at' => $mediaEntity->getCreatedAt()?->format('Y-m-d H:i:s'),
            ];
        }

        // Delete only if not a dry run
        if (!$dryRun && !empty($sample)) {
            $ids = [];
            foreach ($sample as $item) {
                $ids[] = ['id' => $item['id']];
            }
            $this->mediaRepository->delete($ids, $context);
        }


        return [
            'count' => $media->count(),
            'sample' => $sample, // return full sample here
        ];
    }

    public function getName(): string
    {
        return 'Media Cleanup';
    }
}