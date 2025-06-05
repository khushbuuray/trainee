<?php declare(strict_types=1);

namespace IctDataCleanerPro\Service\Cleanup;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;

class PromotionCleanupHandler implements CleanupHandlerInterface
{
    private EntityRepository $promotionRepository;
    private Connection $connection;

    public function __construct(
        EntityRepository $promotionRepository,
        Connection $connection
    ) {
        $this->promotionRepository = $promotionRepository;
        $this->connection = $connection;
    }

    public function cleanup(array $config, bool $dryRun, Context $context): array
    {
        $results = [
            'name' => $this->getName(),
            'items' => []
        ];

        // Clean expired promotions
        if (isset($config['promotionCleanup.expiredMonths'])) {
            $expiredResults = $this->cleanupExpiredPromotions(
                (int) $config['promotionCleanup.expiredMonths'],
                $dryRun,
                $context
            );
            $results['items']['expired_promotions'] = $expiredResults;
        }

        // Clean unused vouchers
        if (isset($config['promotionCleanup.unusedVoucherMonths'])) {
            $voucherResults = $this->cleanupUnusedVouchers(
                (int) $config['promotionCleanup.unusedVoucherMonths'],
                $dryRun,
                $context
            );
            $results['items']['unused_vouchers'] = $voucherResults;
        }

        // Clean orphaned cart rules
        if ($config['cartRuleCleanup.orphaned'] ?? false) {
            $cartRuleResults = $this->cleanupOrphanedCartRules($dryRun, $context);
            $results['items']['orphaned_cart_rules'] = $cartRuleResults;
        }

        return $results;
    }

    private function cleanupExpiredPromotions(int $months, bool $dryRun, Context $context): array
    {
        $date = new \DateTime();
        $date->modify("-{$months} months");

        $sql = <<<SQL
SELECT p.id, pt.name, p.valid_until
FROM promotion p
LEFT JOIN promotion_translation pt ON p.id = pt.promotion_id
LEFT JOIN promotion_order_rule por ON p.id = por.promotion_id
LEFT JOIN `order` o ON por.order_id = o.id
WHERE p.valid_until < :expiredDate
AND p.valid_until < :cleanupDate
AND o.id IS NULL
LIMIT 1000
SQL;

        $promotions = $this->connection->fetchAllAssociative($sql, [
            'expiredDate' => (new \DateTime())->format('Y-m-d H:i:s'),
            'cleanupDate' => $date->format('Y-m-d H:i:s')
        ]);

        if (!$dryRun && !empty($promotions)) {
            $ids = array_map(function ($promotion) {
                return ['id' => $promotion['id']];
            }, $promotions);
            
            $this->promotionRepository->delete($ids, $context);
        }

        return [
            'count' => count($promotions),
            'sample' => array_slice($promotions, 0, 5)
        ];
    }

    private function cleanupUnusedVouchers(int $months, bool $dryRun, Context $context): array
    {
        $date = new \DateTime();
        $date->modify("-{$months} months");

        $countSql = <<<SQL
SELECT COUNT(*) as count
FROM promotion_individual_code pic
LEFT JOIN promotion_order_rule por ON pic.promotion_id = por.promotion_id
WHERE pic.created_at < :date
AND por.promotion_id IS NULL
SQL;

        $countResult = $this->connection->fetchAssociative($countSql, [
            'date' => $date->format('Y-m-d H:i:s')
        ]);

        $count = (int) $countResult['count'];

        $sampleSql = <<<SQL
SELECT pic.id, pic.code, pic.created_at
FROM promotion_individual_code pic
LEFT JOIN promotion_order_rule por ON pic.promotion_id = por.promotion_id
WHERE pic.created_at < :date
AND por.promotion_id IS NULL
LIMIT 5
SQL;

        $samples = $this->connection->fetchAllAssociative($sampleSql, [
            'date' => $date->format('Y-m-d H:i:s')
        ]);

        if (!$dryRun && $count > 0) {
            $deleteSql = <<<SQL
DELETE pic FROM promotion_individual_code pic
LEFT JOIN promotion_order_rule por ON pic.promotion_id = por.promotion_id
WHERE pic.created_at < :date
AND por.promotion_id IS NULL
SQL;
            
            $this->connection->executeStatement($deleteSql, [
                'date' => $date->format('Y-m-d H:i:s')
            ]);
        }

        return [
            'count' => $count,
            'sample' => $samples
        ];
    }

    private function cleanupOrphanedCartRules(bool $dryRun, Context $context): array
    {
        $countSql = <<<SQL
SELECT COUNT(*) as count
FROM rule r
LEFT JOIN promotion_cart_rule pcr ON r.id = pcr.rule_id
LEFT JOIN promotion p ON pcr.promotion_id = p.id
WHERE r.created_at < DATE_SUB(NOW(), INTERVAL 30 DAY)
AND (p.id IS NULL OR p.active = 0)
SQL;

        $countResult = $this->connection->fetchAssociative($countSql);
        $count = (int) $countResult['count'];

        $sampleSql = <<<SQL
SELECT r.id, r.name, r.created_at
FROM rule r
LEFT JOIN promotion_cart_rule pcr ON r.id = pcr.rule_id
LEFT JOIN promotion p ON pcr.promotion_id = p.id
WHERE r.created_at < DATE_SUB(NOW(), INTERVAL 30 DAY)
AND (p.id IS NULL OR p.active = 0)
LIMIT 5
SQL;

        $samples = $this->connection->fetchAllAssociative($sampleSql);

        if (!$dryRun && $count > 0) {
            $deleteSql = <<<SQL
DELETE r FROM rule r
LEFT JOIN promotion_cart_rule pcr ON r.id = pcr.rule_id
LEFT JOIN promotion p ON pcr.promotion_id = p.id
WHERE r.created_at < DATE_SUB(NOW(), INTERVAL 30 DAY)
AND (p.id IS NULL OR p.active = 0)
SQL;
            
            $this->connection->executeStatement($deleteSql);
        }

        return [
            'count' => $count,
            'sample' => $samples
        ];
    }

    public function getName(): string
    {
        return 'Promotion & Discount Cleanup';
    }
}
