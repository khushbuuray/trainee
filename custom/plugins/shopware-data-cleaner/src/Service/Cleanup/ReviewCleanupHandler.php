<?php declare(strict_types=1);

namespace IctDataCleanerPro\Service\Cleanup;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\Uuid\Uuid;

class ReviewCleanupHandler implements CleanupHandlerInterface
{
    private EntityRepository $productReviewRepository;
    private Connection $connection;

    public function __construct(
        EntityRepository $productReviewRepository,
        Connection $connection
    ) {
        $this->productReviewRepository = $productReviewRepository;
        $this->connection = $connection;
    }

    public function cleanup(array $config, bool $dryRun, Context $context): array
    {
        $results = [
            'name' => $this->getName(),
            'items' => []
        ];

        // Clean unapproved reviews
        if (isset($config['reviewCleanup.unapprovedDays'])) {
            $unapprovedResults = $this->cleanupUnapprovedReviews(
                (int) $config['reviewCleanup.unapprovedDays'],
                $dryRun,
                $context
            );
            $results['items']['unapproved_reviews'] = $unapprovedResults;
        }

        return $results;
    }

    private function cleanupUnapprovedReviews(int $days, bool $dryRun, Context $context): array
    {
        $date = new \DateTime();
        $date->modify("-{$days} days");
        $sql = <<<SQL
SELECT pr.id, pr.title,pr.status, pr.created_at
FROM product_review pr
WHERE pr.status = 0 -- 0 typically means unapproved
AND pr.created_at < :date
LIMIT 1000
SQL;

        $reviews = $this->connection->fetchAllAssociative($sql, [
            'date' => $date->format('Y-m-d H:i:s')
        ]);

        if (!$dryRun && !empty($reviews)) {
            $ids = array_map(function ($review) {
                return ['id' => $review['id']];
            }, $reviews);
            
            $this->productReviewRepository->delete($ids, $context);
        }

        // Format sample for preview
    $sample = array_map(function ($review) {
       return [
        'id' => Uuid::fromBytesToHex($review['id']), // Optional: convert binary UUID
        'title' => $review['title'],
        'created_at' => $review['created_at'],
        'status' => (int)$review['status'] === 0 ? 'Unapproved' : 'Approved',

        ];
    }, $reviews);

        return [
            'count' => count($reviews),
            'sample' => $sample
        ];
    }

    public function getName(): string
    {
        return 'Review & Rating Cleanup';
    }
}
