<?php declare(strict_types=1);

namespace IctDataCleanerPro\Service\Cleanup;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\Uuid\Uuid;

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

        // Clean orphaned media
        if (isset($config['mediaCleanup.orphanAgeDays'])) {
            $orphanResults = $this->cleanupOrphanedMedia(
                (int) $config['mediaCleanup.orphanAgeDays'],
                $dryRun,
                $context
            );
            $results['items']['orphaned_media'] = $orphanResults;
        }

        // Clean orphaned thumbnails
        if ($config['mediaCleanup.deleteThumbnails'] ?? false) {
            $thumbnailResults = $this->cleanupOrphanedThumbnails($dryRun, $context);
            $results['items']['orphaned_thumbnails'] = $thumbnailResults;
        }

        return $results;
    }

    private function cleanupOrphanedMedia(int $days, bool $dryRun, Context $context): array
    {
        $date = new \DateTime();
        $date->modify("-{$days} days");

//         $sql = <<<SQL
// SELECT m.id, m.file_name
// FROM media m
// LEFT JOIN product_media pm ON m.id = pm.media_id
// LEFT JOIN category c ON m.id = c.media_id
// LEFT JOIN cms_page cp ON m.id = cp.preview_media_id
// WHERE m.created_at < :date
// AND pm.media_id IS NULL
// AND c.media_id IS NULL
// AND cp.preview_media_id IS NULL
// LIMIT 1000
// SQL;

    $sql = <<<SQL
SELECT LOWER(HEX(m.id)) AS id, m.file_name, m.created_at
FROM media m
LEFT JOIN product_media pm ON m.id = pm.media_id
LEFT JOIN product p ON m.id = p.product_media_id
LEFT JOIN category c ON m.id = c.media_id
LEFT JOIN cms_page cp ON m.id = cp.preview_media_id
LEFT JOIN cms_block cb ON m.id = cb.background_media_id
LEFT JOIN product_manufacturer mf ON m.id = mf.media_id
WHERE m.created_at < :date
  AND pm.media_id IS NULL
  AND p.product_media_id IS NULL
  AND c.media_id IS NULL
  AND cp.preview_media_id IS NULL
  AND cb.background_media_id IS NULL
  AND mf.media_id IS NULL
LIMIT 1000
SQL;

        $media = $this->connection->fetchAllAssociative($sql, [
            'date' => $date->format('Y-m-d H:i:s')
        ]);

//         $media = array_map(function ($item) {
//     $item['id'] = $item['id']; // ✅ Convert UUID properly
//     return $item;
// }, $media);

        if (!$dryRun && !empty($media)) {
            $ids = array_map(function ($item) {
                return ['id' => $item['id']];
            }, $media);
            
            $this->mediaRepository->delete($ids, $context);
        }

        return [
            'count' => count($media),
            'sample' => $media
        ];
    }

    private function cleanupOrphanedThumbnails(bool $dryRun, Context $context): array
    {
        $countSql = <<<SQL
SELECT COUNT(*) as count
FROM media_thumbnail mt
LEFT JOIN media m ON mt.media_id = m.id
WHERE m.id IS NULL
SQL;

        $countResult = $this->connection->fetchAssociative($countSql);
        $count = (int) $countResult['count'];

        $sampleSql = <<<SQL
SELECT mt.id, mt.media_id
FROM media_thumbnail mt
LEFT JOIN media m ON mt.media_id = m.id
WHERE m.id IS NULL
LIMIT 5
SQL;

        $samples = $this->connection->fetchAllAssociative($sampleSql);

        if (!$dryRun && $count > 0) {
            $deleteSql = <<<SQL
DELETE mt FROM media_thumbnail mt
LEFT JOIN media m ON mt.media_id = m.id
WHERE m.id IS NULL
SQL;
            
            $this->connection->executeStatement($deleteSql);
        }

        return [
            'count' => $count,
            'sample' => $samples
        ];
    }

    public function getName(): string
    {
        return 'Media Cleanup';
    }
}
