<?php declare(strict_types=1);

namespace IctDataCleanerPro\Service\Cleanup;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Uuid\Uuid;
use IctDataCleanerPro\Service\CleanupLoggerService;
use Shopware\Core\Checkout\Cart\Cart;
use Shopware\Core\Checkout\Cart\LineItem\LineItem;

class CartCleanupHandler implements CleanupHandlerInterface
{
    private Connection $connection;
    private CleanupLoggerService $logger;

    public function __construct(Connection $connection, CleanupLoggerService $logger)
    {
        $this->connection = $connection;
        $this->logger = $logger;
    }

    /**
     * @param array<string, mixed> $config
     * @return array{name: string, items: array<string, array{count: int, sample: list<array{id: string, name: string, quantity: int, token: string, created_at: string}>}>}
     */
    public function cleanup(array $config, bool $dryRun, Context $context): array
    {
        $results = [
            'name' => $this->getName(),
            'items' => [],
        ];

        if (isset($config['cartCleanup.abandonedDays']) && is_numeric($config['cartCleanup.abandonedDays'])) {
            $days = (int) $config['cartCleanup.abandonedDays'];
            $cartResults = $this->cleanupAbandonedCarts($days, $dryRun, $context);
            $results['items']['abandoned_carts'] = $cartResults;
        }

        return $results;
    }

    /**
     * @return array{count: int, sample: list<array{id: string, name: string, quantity: int, token: string, created_at: string}>}
     */
    private function cleanupAbandonedCarts(int $days, bool $dryRun, Context $context): array
    {
        $date = new \DateTime();
        $date->modify("-{$days} days");

        $cartRepo = $this->connection->createQueryBuilder();
        $cartRepo
            ->select(['token', 'payload', 'created_at'])
            ->from('cart')
            ->where('created_at < :date')
            ->setParameter('date', $date->format('Y-m-d H:i:s'))
            ->setMaxResults(10);

        /** @var list<array{token: string, payload: string|null, created_at: string}> $carts */
        $carts = $cartRepo->executeQuery()->fetchAllAssociative();

        /** @var list<array{id: string, name: string, quantity: int, token: string, created_at: string}> $results */
        $results = [];

        foreach ($carts as $cartRow) {
            $payloadRaw = $cartRow['payload'];

            if (!is_string($payloadRaw)) {
                continue;
            }

            $payload = @unserialize($payloadRaw);

            if (!($payload instanceof Cart)) {
                continue;
            }

            foreach ($payload->getLineItems() as $item) {
                $productId = $item->getReferencedId();
                if (!$productId) {
                    continue;
                }

                $productName = $this->connection->fetchOne(
                    'SELECT name FROM product_translation WHERE product_id = :id',
                    ['id' => Uuid::fromHexToBytes($productId)]
                );

                if (!is_string($productName)) {
                    $productName = (string) $item->getLabel();
                }

                $results[] = [
                    'id' => $productId,
                    'name' => $productName,
                    'quantity' => $item->getQuantity(),
                    'token' => $cartRow['token'],
                    'created_at' => $cartRow['created_at'],
                ];
            }
        }

        $this->logger->logToFile('cart', 'info', [
            'function' => 'cleanupAbandonedCarts',
            'count' => count($results),
            'date_cutoff' => $date->format('Y-m-d H:i:s'),
        ]);

        if (!$dryRun && !empty($results)) {
            try {
                $this->connection->executeStatement(
                    'DELETE FROM cart WHERE created_at < :date',
                    ['date' => $date->format('Y-m-d H:i:s')]
                );

                $this->logger->logSuccess('cart', [
                    'action' => 'delete',
                    'count' => count($results),
                    'cutoff' => $date->format('Y-m-d H:i:s'),
                ]);
            } catch (\Throwable $e) {
                $this->logger->logError('cart', $e, [
                    'action' => 'delete',
                    'cutoff' => $date->format('Y-m-d H:i:s'),
                ]);
            }
        }

        return [
            'count' => count($results),
            'sample' => $results,
        ];
    }

    public function getName(): string
    {
        return 'Cart Cleanup';
    }

       public function getKey(): string
    {
        return 'cartCleanup';
    }
}
