<?php declare(strict_types=1);

namespace IctDataCleanerPro\Service\Cleanup;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;

class NewsletterCleanupHandler implements CleanupHandlerInterface
{
    private EntityRepository $newsletterRecipientRepository;
    private Connection $connection;

    public function __construct(
        EntityRepository $newsletterRecipientRepository,
        Connection $connection
    ) {
        $this->newsletterRecipientRepository = $newsletterRecipientRepository;
        $this->connection = $connection;
    }

    public function cleanup(array $config, bool $dryRun, Context $context): array
    {
        $results = [
            'name' => $this->getName(),
            'items' => []
        ];

        // Clean bounced newsletter recipients
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
        $date = new \DateTime();
        $date->modify("-{$months} months");

        $sql = <<<SQL
SELECT nr.id, nr.email, nr.created_at
FROM newsletter_recipient nr
WHERE nr.status = 'rejected' -- Assuming 'rejected' means bounced
AND nr.created_at < :date
LIMIT 1000
SQL;

        $recipients = $this->connection->fetchAllAssociative($sql, [
            'date' => $date->format('Y-m-d H:i:s')
        ]);

        if (!$dryRun && !empty($recipients)) {
            $ids = array_map(function ($recipient) {
                return ['id' => $recipient['id']];
            }, $recipients);
            
            $this->newsletterRecipientRepository->delete($ids, $context);
        }

        return [
            'count' => count($recipients),
            'sample' => array_slice($recipients, 0, 5)
        ];
    }

    public function getName(): string
    {
        return 'Newsletter Recipient Cleanup';
    }
}
