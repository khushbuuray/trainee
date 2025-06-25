<?php declare(strict_types=1);

namespace IctDataCleanerPro\Service\Cleanup;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\RangeFilter;
use Shopware\Core\Framework\Uuid\Uuid;

class ReviewCleanupHandler implements CleanupHandlerInterface
{
    private EntityRepository $productReviewRepository;

    public function __construct(
        EntityRepository $productReviewRepository
    ) {
        $this->productReviewRepository = $productReviewRepository;
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
        $criteria->addFilter(new EqualsFilter('status', false)); // unapproved
        $criteria->addFilter(new RangeFilter('createdAt', [RangeFilter::LT => $cutoff->format(DATE_ATOM)]));
        $criteria->setLimit(1000);

        $reviews = $this->productReviewRepository->search($criteria, $context);

        $sample = [];

//        dd($reviews);
        foreach ($reviews as $review) {
            $sample[] = [
                'id' => $review->getId(),
                'title' => $review->getTitle(),
                'created_at' => $review->getCreatedAt()?->format('Y-m-d H:i:s'),
                'status' => $review->getStatus() ? 'Approved' : 'Unapproved',
            ];
        }

        if (!$dryRun && $reviews->count() > 0) {
            $ids = [];
            foreach ($reviews->getElements() as $review) {
                if ($review && $review->getId()) {
                    $ids[] = ['id' => $review->getId()];
                }
            }

            if (!empty($ids)) {
                $this->productReviewRepository->delete($ids, $context);
            }
        }


        return [
            'count' => $reviews->count(),
            'sample' => $sample
        ];
    }

    public function getName(): string
    {
        return 'Review & Rating Cleanup';
    }
}