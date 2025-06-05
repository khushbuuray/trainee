<?php declare(strict_types=1);

namespace IctDataCleanerPro\Service\Cleanup;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;

class CmsCleanupHandler implements CleanupHandlerInterface
{
    private EntityRepository $cmsPageRepository;
    private Connection $connection;

    public function __construct(
        EntityRepository $cmsPageRepository,
        Connection $connection
    ) {
        $this->cmsPageRepository = $cmsPageRepository;
        $this->connection = $connection;
    }

    public function cleanup(array $config, bool $dryRun, Context $context): array
    {
        $results = [
            'name' => $this->getName(),
            'items' => []
        ];

        // Clean CMS pages never viewed
        if (isset($config['cmsPageCleanup.neverViewedMonths'])) {
            $neverViewedResults = $this->cleanupNeverViewedPages(
                (int) $config['cmsPageCleanup.neverViewedMonths'],
                $dryRun,
                $context
            );
            $results['items']['never_viewed_pages'] = $neverViewedResults;
        }

        // Clean unpublished CMS drafts
        if (isset($config['cmsPageCleanup.unpublishedDraftsMonths'])) {
            $draftResults = $this->cleanupUnpublishedDrafts(
                (int) $config['cmsPageCleanup.unpublishedDraftsMonths'],
                $dryRun,
                $context
            );
            $results['items']['unpublished_drafts'] = $draftResults;
        }

        return $results;
    }

    private function cleanupNeverViewedPages(int $months, bool $dryRun, Context $context): array
    {
        // This is a placeholder - actual tracking of page views is complex
        // and would require a separate tracking mechanism.
        // For now, we'll assume no tracking and return 0.
        return [
            'count' => 0,
            'sample' => [],
            'note' => 'CMS page view tracking not implemented'
        ];
    }

    private function cleanupUnpublishedDrafts(int $months, bool $dryRun, Context $context): array
    {
        $date = new \DateTime();
        $date->modify("-{$months} months");

        $sql = <<<SQL
SELECT cp.id, cpt.name, cp.created_at
FROM cms_page cp
LEFT JOIN cms_page_translation cpt ON cp.id = cpt.cms_page_id
WHERE cp.locked = 0 
AND cp.type != 'product_list' -- Exclude category layout pages
AND cp.created_at < :date
AND NOT EXISTS (
    SELECT 1 FROM category cat WHERE cat.cms_page_id = cp.id
)
LIMIT 1000
SQL;

        $pages = $this->connection->fetchAllAssociative($sql, [
            'date' => $date->format('Y-m-d H:i:s')
        ]);

        if (!$dryRun && !empty($pages)) {
            $ids = array_map(function ($page) {
                return ['id' => $page['id']];
            }, $pages);
            
            $this->cmsPageRepository->delete($ids, $context);
        }

        return [
            'count' => count($pages),
            'sample' => array_slice($pages, 0, 5)
        ];
    }

    public function getName(): string
    {
        return 'CMS Content & Page Cleanup';
    }
}
