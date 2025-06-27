<?php declare(strict_types=1);

namespace IctDataCleanerPro\Service\Cleanup;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\RangeFilter;
use IctDataCleanerPro\Service\CleanupLoggerService;

class ReviewCleanupHandler implements CleanupHandlerInterface
{
    private EntityRepository $productReviewRepository;
    private CleanupLoggerService $logger;

    public function __construct(
        EntityRepository $productReviewRepository,
        CleanupLoggerService $logger
    ) {
        $this->productReviewRepository = $productReviewRepository;
        $this->logger = $logger;
    }

    public function cleanup(array $config, bool $dryRun, Context $context): array
    {
        $results = [
            'name' => $this->getName(),
            'items' => []
        ];

        if (isset($config['reviewCleanup.unapprovedDays'])) {
            $results['items']['unapproved_reviews'] = $this->cleanupUnapprovedReviews(
                (int) $config['reviewCleanup.unapprovedDays'],
                $dryRun,
                $context
            );
        }

        return $results;
    }

    private function cleanupUnapprovedReviews(int $days, bool $dryRun, Context $context): array
    {
        $cutoff = (new \DateTime())->modify("-{$days} days");

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('status', false));
        $criteria->addFilter(new RangeFilter('createdAt', [RangeFilter::LT => $cutoff->format(DATE_ATOM)]));
        $criteria->setLimit(1000);

        $reviews = $this->productReviewRepository->search($criteria, $context);

        $sample = [];
        foreach ($reviews as $review) {
            $sample[] = [
                'id' => $review->getId(),
                'title' => $review->getTitle(),
                'created_at' => $review->getCreatedAt()?->format('Y-m-d H:i:s'),
                'status' => $review->getStatus() ? 'Approved' : 'Unapproved',
            ];
        }

        $this->logger->logToFile('reviews', 'info', [
            'function' => 'cleanupUnapprovedReviews',
            'count' => $reviews->count(),
            'cutoff_date' => $cutoff->format(DATE_ATOM),
        ]);

        if (!$dryRun && $reviews->count() > 0) {
            $ids = [];
            foreach ($reviews->getElements() as $review) {
                if ($review && $review->getId()) {
                    $ids[] = ['id' => $review->getId()];
                }
            }

            if (!empty($ids)) {
                try {
                    $event = $this->productReviewRepository->delete($ids, $context);

                    // Confirm deletion
                    $remaining = $this->productReviewRepository->search(
                        new Criteria(array_column($ids, 'id')),
                        $context
                    );

                    $this->logger->logToFile('reviews', 'info', [
                        'deleted' => array_column($ids, 'id'),
                        'remaining_count' => $remaining->getTotal(),
                    ]);

                } catch (\Exception $e) {
                    $this->logger->logError('reviews', $e, [
                        'action' => 'delete_unapproved',
                        'ids' => array_column($ids, 'id'),
                    ]);
                }
            }
        }

        return [
            'count' => $reviews->count(),
            'sample' => $sample,
        ];
    }

    public function getName(): string
    {
        return 'Review & Rating Cleanup';
    }
}
