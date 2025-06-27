<?php declare(strict_types=1);

namespace IctDataCleanerPro\Service\Cleanup;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\RangeFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\MultiFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\NotFilter;
use IctDataCleanerPro\Service\CleanupLoggerService;

class PromotionCleanupHandler implements CleanupHandlerInterface
{
    private EntityRepository $promotionRepository;
    private EntityRepository $ruleRepository;
    private CleanupLoggerService $logger;
    private Connection $connection;


    public function __construct(
        EntityRepository $promotionRepository,
        EntityRepository $ruleRepository,
        CleanupLoggerService $logger,
        Connection $connection
    ) {
        $this->promotionRepository = $promotionRepository;
        $this->ruleRepository = $ruleRepository;
        $this->logger = $logger;
        $this->connection = $connection;

    }

    public function cleanup(array $config, bool $dryRun, Context $context): array
    {
        $results = [
            'name' => $this->getName(),
            'items' => []
        ];

        if (isset($config['promotionCleanup.expiredMonths'])) {
            $expiredResults = $this->cleanupExpiredPromotions(
                (int) $config['promotionCleanup.expiredMonths'],
                $dryRun,
                $context
            );
            $results['items']['expired_promotions'] = $expiredResults;
        }

        if (isset($config['promotionCleanup.unusedVoucherMonths'])) {
            $voucherResults = $this->cleanupUnusedVouchers(
                (int) $config['promotionCleanup.unusedVoucherMonths'],
                $dryRun,
                $context
            );
            $results['items']['unused_vouchers'] = $voucherResults;
        }

        if ($config['cartRuleCleanup.orphaned'] ?? false) {
            $cartRuleResults = $this->cleanupOrphanedCartRules($dryRun, $context);
            $results['items']['orphaned_cart_rules'] = $cartRuleResults;
        }

        return $results;
    }

    private function cleanupExpiredPromotions(int $months, bool $dryRun, Context $context): array
    {
        $cutoffDate = (new \DateTime())->modify("-{$months} months");

        $criteria = new Criteria();
        $criteria->setLimit(1000);
        $criteria->addFilter(new RangeFilter('validUntil', [RangeFilter::LT => $cutoffDate->format(\DATE_ATOM)]));
        $criteria->addAssociation('translations');

        $promotions = $this->promotionRepository->search($criteria, $context);

        $sample = [];
        foreach ($promotions as $promo) {
            $sample[] = [
                'id' => $promo->getId(),
                'name' => $promo->getTranslated()['name'] ?? 'N/A',
                'valid_until' => $promo->getValidUntil()?->format('Y-m-d H:i:s') ?? null,
            ];
        }

        $this->logger->logToFile('promotions', 'info', [
            'function' => 'cleanupExpiredPromotions',
            'count' => count($sample),
            'cutoff' => $cutoffDate->format(DATE_ATOM),
        ]);

        if (!$dryRun && !empty($sample)) {
            $ids = array_map(fn($p) => ['id' => $p['id']], $sample);
            try {
                $this->promotionRepository->delete($ids, $context);
                $this->logger->logSuccess('promotions', [
                    'action' => 'delete_expired',
                    'count' => count($ids),
                    'ids' => array_column($ids, 'id'),
                ]);
            } catch (\Exception $e) {
                $this->logger->logError('promotions', $e, [
                    'action' => 'delete_expired',
                    'ids' => array_column($ids, 'id'),
                ]);
            }
        }

        return [
            'count' => count($sample),
            'sample' => array_slice($sample, 0, 5),
        ];
    }

    private function cleanupUnusedVouchers(int $months, bool $dryRun, Context $context): array
    {
        $cutoffDate = (new \DateTime())->modify("-{$months} months");

        $criteria = new Criteria();
        $criteria->setLimit(1000);
        $criteria->addFilter(new RangeFilter('createdAt', [RangeFilter::LT => $cutoffDate->format(\DATE_ATOM)]));
        $criteria->addFilter(new EqualsFilter('orderLineItems.id', null));
        $criteria->addAssociation('orderLineItems');

        $voucherCodes = $this->promotionRepository->search($criteria, $context);

        $sample = [];
        foreach ($voucherCodes as $code) {
            $sample[] = [
                'id' => $code->getId(),
                'code' => $code->getCode(),
                'name' => $code->getName(),
                'created_at' => $code->getCreatedAt()?->format('Y-m-d H:i:s'),
            ];
        }

        $this->logger->logToFile('promotions', 'info', [
            'function' => 'cleanupUnusedVouchers',
            'count' => count($sample),
            'cutoff' => $cutoffDate->format(DATE_ATOM),
        ]);

        if (!$dryRun && !empty($sample)) {
            $ids = array_map(fn($p) => ['id' => $p['id']], $sample);
            try {
                $this->promotionRepository->delete($ids, $context);
                $this->logger->logSuccess('promotions', [
                    'action' => 'delete_unused_vouchers',
                    'count' => count($ids),
                    'ids' => array_column($ids, 'id'),
                ]);
            } catch (\Exception $e) {
                $this->logger->logError('promotions', $e, [
                    'action' => 'delete_unused_vouchers',
                    'ids' => array_column($ids, 'id'),
                ]);
            }
        }

        return [
            'count' => count($sample),
            'sample' => $sample,
        ];
    }

    private function cleanupOrphanedCartRules(bool $dryRun, Context $context): array
    {
        $cutoff = (new \DateTime())->modify('-30 days');

        $criteria = new Criteria();
        $criteria->setLimit(100);
        $criteria->addFilter(new RangeFilter('createdAt', [RangeFilter::LT => $cutoff->format(\DATE_ATOM)]));
        $criteria->addFilter(new MultiFilter('AND', [
            new EqualsFilter('flowSequences.id', null),
            new EqualsFilter('productPrices.id', null),
            new EqualsFilter('shippingMethodPrices.id', null),
            new EqualsFilter('shippingMethods.id', null),
            new EqualsFilter('paymentMethods.id', null),
        ]));

        $rules = $this->ruleRepository->search($criteria, $context);

        $sample = [];
        foreach ($rules as $rule) {
            $sample[] = [
                'id' => $rule->getId(),
                'name' => $rule->getName(),
                'created_at' => $rule->getCreatedAt()?->format('Y-m-d H:i:s'),
            ];
        }

        $this->logger->logToFile('promotions', 'info', [
            'function' => 'cleanupOrphanedCartRules',
            'count' => count($sample),
            'cutoff' => $cutoff->format(DATE_ATOM),
        ]);

        if (!$dryRun && !empty($sample)) {
            $ids = array_map(fn($r) => ['id' => $r['id']], $sample);
            try {
                $this->ruleRepository->delete($ids, $context);
                $this->logger->logSuccess('promotions', [
                    'action' => 'delete_orphaned_rules',
                    'count' => count($ids),
                    'ids' => array_column($ids, 'id'),
                ]);
            } catch (\Exception $e) {
                $this->logger->logError('promotions', $e, [
                    'action' => 'delete_orphaned_rules',
                    'ids' => array_column($ids, 'id'),
                ]);
            }
        }

        return [
            'count' => count($sample),
            'sample' => $sample,
        ];
    }

    public function getName(): string
    {
        return 'Promotion & Discount Cleanup';
    }
}
