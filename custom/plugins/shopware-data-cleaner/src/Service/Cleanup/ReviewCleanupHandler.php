<?php declare(strict_types=1);

namespace IctDataCleanerPro\Service\Cleanup;

use DateTime;
use Shopware\Core\Content\Product\Aggregate\ProductReview\ProductReviewCollection;
use Shopware\Core\Content\Product\Aggregate\ProductReview\ProductReviewEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\RangeFilter;
use IctDataCleanerPro\Service\CleanupLoggerService;

class ReviewCleanupHandler implements CleanupHandlerInterface
{
    /** @var EntityRepository<ProductReviewCollection> */
    private readonly EntityRepository $productReviewRepository;

    private readonly CleanupLoggerService $logger;

    /**
     * @param EntityRepository<ProductReviewCollection> $productReviewRepository
     */
    public function __construct(
        EntityRepository $productReviewRepository,
        CleanupLoggerService $logger
    ) {
        $this->productReviewRepository = $productReviewRepository;
        $this->logger = $logger;
    }

    /**
     * @param array<string, mixed> $config
     * @return array{name: string, items: array<string, array{count: int, sample: list<array{id: string, name: string}>}>}
     */
    public function cleanup(array $config, bool $dryRun, Context $context): array
    {
        $items = [];

        if (isset($config['reviewCleanup.unapprovedDays']) && is_numeric($config['reviewCleanup.unapprovedDays'])) {
            $items['unapproved_reviews'] = $this->cleanupUnapprovedReviews(
                (int) $config['reviewCleanup.unapprovedDays'],
                $dryRun,
                $context
            );
        }

        return [
            'name' => $this->getName(),
            'items' => $items,
        ];
    }

    /**
     * @return array{count: int, sample: list<array{id: string, name: string}>}
     */
    private function cleanupUnapprovedReviews(int $days, bool $dryRun, Context $context): array
    {
        $cutoff = (new DateTime())->modify("-{$days} days");

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('status', false));
        $criteria->addFilter(new RangeFilter('createdAt', [RangeFilter::LT => $cutoff->format(DATE_ATOM)]));
        $criteria->setLimit(1000);

        /** @var ProductReviewCollection $reviews */
        $reviews = $this->productReviewRepository->search($criteria, $context)->getEntities();

        $sample = [];
        foreach ($reviews as $review) {
            /** @var ProductReviewEntity $review */
            $sample[] = [
                'id' => $review->getId(),
                'name' => (string) $review->getTitle(),
            ];
        }

        $this->logger->logToFile('reviews', 'info', [
            'function' => 'cleanupUnapprovedReviews',
            'count' => $reviews->count(),
            'cutoff_date' => $cutoff->format(DATE_ATOM),
        ]);

        if (!$dryRun && $reviews->count() > 0) {
            $ids = [];

            foreach ($reviews as $review) {
                /** @var ProductReviewEntity $review */
                $ids[] = ['id' => $review->getId()];
            }

            if (!empty($ids)) {
                try {
                    $this->productReviewRepository->delete($ids, $context);

                    $remaining = $this->productReviewRepository->search(
                        new Criteria(array_column($ids, 'id')),
                        $context
                    );

                    $this->logger->logToFile('reviews', 'info', [
                        'deleted' => array_column($ids, 'id'),
                        'remaining_count' => $remaining->getTotal(),
                    ]);
                } catch (\Throwable $e) {
                    $this->logger->logError('reviews', $e, [
                        'action' => 'delete_unapproved',
                        'ids' => array_column($ids, 'id'),
                    ]);
                }
            }
        }

        return [
            'count' => $reviews->count(),
            'sample' => array_slice($sample, 0, 5),
        ];
    }

    public function getName(): string
    {
        return 'Review & Rating Cleanup';
    }
}
