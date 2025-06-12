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

//     private function cleanupAbandonedCarts(int $days, bool $dryRun, Context $context): array
//     {
//         $date = new \DateTime();
//         $date->modify("-{$days} days");

//         // Get cart count first
// //         $countSql = <<<SQL
// // SELECT COUNT(*) as count
// // FROM cart 
// // WHERE created_at < :date
// // SQL;

// //         $countResult = $this->connection->fetchAssociative($countSql, [
// //             'date' => $date->format('Y-m-d H:i:s')
// //         ]);

//         // $count = (int) $countResult['count'];
//         // dd($count);

//         // Get sample data
//        $sql = <<<SQL
// SELECT token, created_at, payload
// FROM cart
// WHERE created_at < :date
// LIMIT 10
// SQL;

//  $carts = $this->connection->fetchAllAssociative($sql, [
//         'date' => $date->format('Y-m-d H:i:s'),
//     ]);

//     $products = [];

//     foreach ($carts as $cart) {
//     dd($cart);

//         $payload = json_decode($cart['payload'], true);

//         if (!is_array($payload)) {
//             continue;
//         }

//         $lineItems = $payload['lineItems'] ?? [];

//         foreach ($lineItems as $item) {
//             if (($item['type'] ?? '') !== 'product') {
//                 continue;
//             }

//             $products[] = [
//                 'cart_token' => $cart['token'],
//                 'product_id' => $item['id'] ?? '',
//                 'product_number' => $item['payload']['productNumber'] ?? '',
//                 'product_name' => $item['label'] ?? '',
//                 'quantity' => $item['quantity'] ?? 1,
//                 'added_at' => $cart['created_at'],
//             ];
//         }
//     }

//     return [
//         'count' => count($products),
//         'sample' => $products,
//     ];

// //         $samples = $this->connection->fetchAllAssociative($sampleSql, [
// //             'date' => $date->format('Y-m-d H:i:s')
// //         ]);

// //         if (!$dryRun && $count > 0) {
// //             $deleteSql = <<<SQL
// // DELETE FROM cart 
// // WHERE created_at < :date
// // SQL;
            
// //             $this->connection->executeStatement($deleteSql, [
// //                 'date' => $date->format('Y-m-d H:i:s')
// //             ]);
// //         }

// //         return [
// //             'count' => $count,
// //             'sample' => $samples
// //         ];
//     }

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
