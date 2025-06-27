<?php declare(strict_types=1);

namespace IctDataCleanerPro\Service\Cleanup;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\RangeFilter;
use IctDataCleanerPro\Service\CleanupLoggerService;

class NewsletterCleanupHandler implements CleanupHandlerInterface
{
    private EntityRepository $newsletterRecipientRepository;
    private CleanupLoggerService $logger;

    public function __construct(
        EntityRepository $newsletterRecipientRepository,
        CleanupLoggerService $logger
    ) {
        $this->newsletterRecipientRepository = $newsletterRecipientRepository;
        $this->logger = $logger;
    }

    public function cleanup(array $config, bool $dryRun, Context $context): array
    {
        $results = [
            'name' => $this->getName(),
            'items' => []
        ];

        if (isset($config['newsletterCleanup.bouncedMonths'])) {
            $bouncedResults = $this->cleanupBouncedRecipients(
                (int) $config['newsletterCleanup.bouncedMonths'],
                $dryRun,
                $context
            );
            $results['items']['bounced_recipients'] = $bouncedResults;
        }

        return $results;
    }

    private function cleanupBouncedRecipients(int $months, bool $dryRun, Context $context): array
    {
        $cutoffDate = (new \DateTime())->modify("-{$months} months");

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('status', 'rejected'));
        $criteria->addFilter(new RangeFilter('createdAt', [RangeFilter::LT => $cutoffDate->format(\DATE_ATOM)]));
        $criteria->setLimit(1000);

        $recipients = $this->newsletterRecipientRepository->search($criteria, $context);

        $sample = [];
        foreach ($recipients as $recipient) {
            $sample[] = [
                'id' => $recipient->getId(),
                'email' => $recipient->getEmail(),
                'created_at' => $recipient->getCreatedAt()?->format('Y-m-d H:i:s')
            ];
        }

        $this->logger->logToFile('newsletter', 'info', [
            'function' => 'cleanupBouncedRecipients',
            'count' => $recipients->count(),
            'cutoff_date' => $cutoffDate->format(DATE_ATOM),
        ]);

        if (!$dryRun && $recipients->count() > 0) {
            $ids = array_map(
                fn($e) => ['id' => $e->getId()],
                array_values($recipients->getElements())
            );

            if (!empty($ids)) {
                try {
                    $this->newsletterRecipientRepository->delete($ids, $context);

                    $this->logger->logSuccess('newsletter', [
                        'action' => 'delete_bounced',
                        'count' => count($ids),
                        'ids' => array_column($ids, 'id'),
                    ]);
                } catch (\Exception $e) {
                    $this->logger->logError('newsletter', $e, [
                        'action' => 'delete_bounced',
                        'ids' => array_column($ids, 'id'),
                    ]);
                }
            }
        }

        return [
            'count' => $recipients->count(),
            'sample' => array_slice($sample, 0, 5)
        ];
    }

    public function getName(): string
    {
        return 'Newsletter Recipient Cleanup';
    }
}
