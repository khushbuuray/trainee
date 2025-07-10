<?php declare(strict_types=1);

namespace ICTECHDataCleanerPro\Service\Cleanup;

use DateTime;
use Shopware\Core\Checkout\Promotion\PromotionCollection;
use Shopware\Core\Checkout\Promotion\PromotionEntity;
use Shopware\Core\Content\Rule\RuleCollection;
use Shopware\Core\Content\Rule\RuleEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\MultiFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\RangeFilter;
use ICTECHDataCleanerPro\Service\CleanupLoggerService;

class PromotionCleanupHandler implements CleanupHandlerInterface
{
    /** @var EntityRepository<PromotionCollection> */
    private readonly EntityRepository $promotionRepository;

    /** @var EntityRepository<RuleCollection> */
    private readonly EntityRepository $ruleRepository;

    private readonly CleanupLoggerService $logger;

    /**
     * @param EntityRepository<PromotionCollection> $promotionRepository
     * @param EntityRepository<RuleCollection> $ruleRepository
     */
    public function __construct(
        EntityRepository $promotionRepository,
        EntityRepository $ruleRepository,
        CleanupLoggerService $logger
    ) {
        $this->promotionRepository = $promotionRepository;
        $this->ruleRepository = $ruleRepository;
        $this->logger = $logger;
    }

    /**
     * @param array<string, mixed> $config
     * @return array{name: string, items: array<string, array{count: int, sample: list<array<string, string|null>>}>}
     */
    public function cleanup(array $config, bool $dryRun, Context $context): array
    {
        $items = [];

        $expired = $config['promotionCleanup.expiredMonths'] ?? null;
        if (is_numeric($expired)) {
            $items['expired_promotions'] = $this->cleanupExpiredPromotions((int) $expired, $dryRun, $context);
        }

        $unused = $config['promotionCleanup.unusedVoucherMonths'] ?? null;
        if (is_numeric($unused)) {
            $items['unused_vouchers'] = $this->cleanupUnusedVouchers((int) $unused, $dryRun, $context);
        }

        if (!empty($config['cartRuleCleanup.orphaned'])) {
            $items['orphaned_cart_rules'] = $this->cleanupOrphanedCartRules($dryRun, $context);
        }

        return [
            'name' => $this->getName(),
            'items' => $items,
        ];
    }

    /**
     * @return array{count: int, sample: list<array{id: string, name: string, valid_until?: string|null}>}
     */
    private function cleanupExpiredPromotions(int $months, bool $dryRun, Context $context): array
    {
        $cutoff = (new DateTime())->modify("-{$months} months");

        $criteria = new Criteria();
        $criteria->addFilter(new RangeFilter('validUntil', [RangeFilter::LT => $cutoff->format(DATE_ATOM)]));
        $criteria->addAssociation('translations');
        $criteria->setLimit(10000);

        /** @var PromotionCollection $promotions */
        $promotions = $this->promotionRepository->search($criteria, $context)->getEntities();

        $sample = [];
        foreach ($promotions as $promo) {
            /** @var PromotionEntity $promo */
            $name = $promo->getTranslated()['name'] ?? 'N/A';
            $sample[] = [
                'id' => $promo->getId(),
                'name' => is_string($name) ? $name : 'N/A',
                'valid_until' => $promo->getValidUntil()?->format('Y-m-d H:i:s'),
            ];
        }

        if (!$dryRun && count($sample) > 0) {
            $ids = array_map(static fn(array $p): array => ['id' => $p['id']], $sample);
            try {
                $this->promotionRepository->delete($ids, $context);
                $this->logger->logSuccess('promotions', [
                    'action' => 'delete_expired',
                    'count' => count($ids),
                    'ids' => array_column($ids, 'id'),
                ]);
            } catch (\Throwable $e) {
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

    /**
     * @return array{count: int, sample: list<array{id: string, name: string, code: string|null, created_at: string|null}>}
     */
    private function cleanupUnusedVouchers(int $months, bool $dryRun, Context $context): array
    {
        $cutoff = (new DateTime())->modify("-{$months} months");

        $criteria = new Criteria();
        $criteria->addFilter(new RangeFilter('createdAt', [RangeFilter::LT => $cutoff->format(DATE_ATOM)]));
        $criteria->addFilter(new EqualsFilter('orderLineItems.id', null));
        $criteria->addAssociation('orderLineItems');
        $criteria->setLimit(10000);

        /** @var PromotionCollection $promotions */
        $promotions = $this->promotionRepository->search($criteria, $context)->getEntities();

        $sample = [];
        foreach ($promotions as $promo) {
            /** @var PromotionEntity $promo */
            $name = $promo->getTranslated()['name'] ?? 'N/A';
            $sample[] = [
                'id' => $promo->getId(),
                'name' => is_string($name) ? $name : 'N/A',
                'code' => $promo->getCode(),
                'created_at' => $promo->getCreatedAt()?->format('Y-m-d H:i:s'),
            ];
        }

        if (!$dryRun && count($sample) > 0) {
            $ids = array_map(static fn(array $p): array => ['id' => $p['id']], $sample);

            try {
                $this->promotionRepository->delete($ids, $context);
                $this->logger->logSuccess('promotions', [
                    'action' => 'delete_unused_vouchers',
                    'count' => count($ids),
                    'ids' => array_column($ids, 'id'),
                ]);
            } catch (\Throwable $e) {
                $this->logger->logError('promotions', $e, [
                    'action' => 'delete_unused_vouchers',
                    'ids' => array_column($ids, 'id'),
                ]);
            }
        }

        return [
            'count' => count($sample),
            'sample' => array_slice($sample, 0, 5),
        ];
    }

    /**
     * @return array{count: int, sample: list<array{id: string, name: string, created_at: string|null}>}
     */
    private function cleanupOrphanedCartRules(bool $dryRun, Context $context): array
    {
        $cutoff = (new DateTime())->modify("-30 days");

        $criteria = new Criteria();
        $criteria->addFilter(new RangeFilter('createdAt', [RangeFilter::LT => $cutoff->format(DATE_ATOM)]));
        $criteria->addFilter(new MultiFilter('AND', [
            new EqualsFilter('flowSequences.id', null),
            new EqualsFilter('productPrices.id', null),
            new EqualsFilter('shippingMethodPrices.id', null),
            new EqualsFilter('shippingMethods.id', null),
            new EqualsFilter('paymentMethods.id', null),
        ]));
        $criteria->setLimit(10000);

        /** @var RuleCollection $rules */
        $rules = $this->ruleRepository->search($criteria, $context)->getEntities();

        $sample = [];
        foreach ($rules as $rule) {
            /** @var RuleEntity $rule */
            $sample[] = [
                'id' => $rule->getId(),
                'name' => (string) $rule->getName(),
                'created_at' => $rule->getCreatedAt()?->format('Y-m-d H:i:s'),
            ];
        }

        if (!$dryRun && count($sample) > 0) {
            $ids = array_map(static fn(array $r): array => ['id' => $r['id']], $sample);

            try {
                $this->ruleRepository->delete($ids, $context);
                $this->logger->logSuccess('promotions', [
                    'action' => 'delete_orphaned_rules',
                    'count' => count($ids),
                    'ids' => array_column($ids, 'id'),
                ]);
            } catch (\Throwable $e) {
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

    public function getKey(): string
    {
        return 'promotionCleanup';
    }
}
