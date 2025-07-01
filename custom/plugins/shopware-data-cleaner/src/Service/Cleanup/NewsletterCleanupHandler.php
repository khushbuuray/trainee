<?php declare(strict_types=1);

namespace IctDataCleanerPro\Service\Cleanup;

use DateTime;
use Shopware\Core\Content\Newsletter\Aggregate\NewsletterRecipient\NewsletterRecipientCollection;
use Shopware\Core\Content\Newsletter\Aggregate\NewsletterRecipient\NewsletterRecipientEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\RangeFilter;
use IctDataCleanerPro\Service\CleanupLoggerService;

class NewsletterCleanupHandler implements CleanupHandlerInterface
{
    /** @var EntityRepository<NewsletterRecipientCollection> */
    private readonly EntityRepository $newsletterRecipientRepository;

    private readonly CleanupLoggerService $logger;

    /**
     * @param EntityRepository<NewsletterRecipientCollection> $newsletterRecipientRepository
     */
    public function __construct(
        EntityRepository $newsletterRecipientRepository,
        CleanupLoggerService $logger
    ) {
        $this->newsletterRecipientRepository = $newsletterRecipientRepository;
        $this->logger = $logger;
    }

    /**
     * @param array<string, mixed> $config
     * @return array{name: string, items: array<string, array{count: int, sample: list<array{id: string, name: string}>}>}
     */
    public function cleanup(array $config, bool $dryRun, Context $context): array
    {
        $items = [];

        $monthsRaw = $config['newsletterCleanup.bouncedMonths'] ?? null;
        $months = is_numeric($monthsRaw) ? (int) $monthsRaw : null;

        if ($months !== null) {
            $items['bounced_recipients'] = $this->cleanupBouncedRecipients($months, $dryRun, $context);
        }

        return [
            'name' => $this->getName(),
            'items' => $items,
        ];
    }

    /**
     * @return array{count: int, sample: list<array{id: string, name: string}>}
     */
    private function cleanupBouncedRecipients(int $months, bool $dryRun, Context $context): array
    {
        $cutoffDate = (new DateTime())->modify("-{$months} months");

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('status', 'rejected'));
        $criteria->addFilter(new RangeFilter('createdAt', [RangeFilter::LT => $cutoffDate->format(DATE_ATOM)]));
        $criteria->setLimit(1000);

        /** @var NewsletterRecipientCollection $recipients */
        $recipients = $this->newsletterRecipientRepository->search($criteria, $context)->getEntities();

        /** @var list<array{id: string, name: string}> $sample */
        $sample = [];
        foreach ($recipients as $recipient) {
            /** @var NewsletterRecipientEntity $recipient */
            $sample[] = [
                'id' => $recipient->getId(),
                'name' => $recipient->getEmail(),
            ];
        }

        $this->logger->logToFile('newsletter', 'info', [
            'function' => 'cleanupBouncedRecipients',
            'count' => $recipients->count(),
            'cutoff_date' => $cutoffDate->format(DATE_ATOM),
        ]);

        if (!$dryRun && $recipients->count() > 0) {
            $ids = array_map(
                static fn(NewsletterRecipientEntity $e): array => ['id' => $e->getId()],
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
                } catch (\Throwable $e) {
                    $this->logger->logError('newsletter', $e, [
                        'action' => 'delete_bounced',
                        'ids' => array_column($ids, 'id'),
                    ]);
                }
            }
        }

        return [
            'count' => $recipients->count(),
            'sample' => $sample,
        ];
    }

    public function getName(): string
    {
        return 'Newsletter Recipient Cleanup';
    }

    public function getKey(): string
    {
        return 'newsletterCleanup';
    }
}
