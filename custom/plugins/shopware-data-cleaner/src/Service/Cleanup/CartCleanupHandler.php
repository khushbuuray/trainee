<?php declare(strict_types=1);

namespace IctDataCleanerPro\Service\Cleanup;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Uuid\Uuid;


class CartCleanupHandler implements CleanupHandlerInterface
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function cleanup(array $config, bool $dryRun, Context $context): array
    {
    
        $results = [
            'name' => $this->getName(),
            'items' => []
        ];

        // Clean abandoned carts
        if (isset($config['cartCleanup.abandonedDays'])) {
            $cartResults = $this->cleanupAbandonedCarts(
                (int) $config['cartCleanup.abandonedDays'],
                $dryRun,
                $context
            );
            $results['items']['abandoned_carts'] = $cartResults;
        }

        return $results;
    }


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
        ->setMaxResults(10); // optional limit for preview

    $carts = $cartRepo->executeQuery()->fetchAllAssociative();
    $results = [];

    foreach ($carts as $cartRow) {
        $payload = unserialize($cartRow['payload']);
        if (!($payload instanceof \Shopware\Core\Checkout\Cart\Cart)) {
            continue;
        }

        foreach ($payload->getLineItems() as $item) {
            $productId = $item->getReferencedId();
            if (!$productId) {
                continue;
            }

            // Fetch translated product name (fallback to label if needed)
            $productName = $this->connection->fetchOne(
                'SELECT name FROM product_translation WHERE product_id = :id',
                ['id' => Uuid::fromHexToBytes($productId)]
            ) ?: $item->getLabel();

            $results[] = [
                'id' => $productId,
                'name' => $productName,
                'quantity' => $item->getQuantity(),
                'token' => $cartRow['token'],
                'created_at' => $cartRow['created_at'],
            ];
        }
    }

    // if (!$dryRun && !empty($results)) {
    //     $this->connection->executeStatement(
    //         'DELETE FROM cart WHERE created_at < :date',
    //         ['date' => $date->format('Y-m-d H:i:s')]
    //     );
    // }

    return [
        'count' => count($results),
        'sample' => $results,
    ];
}



    public function getName(): string
    {
        return 'Cart Cleanup';
    }
}
