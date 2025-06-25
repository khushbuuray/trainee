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

class PromotionCleanupHandler implements CleanupHandlerInterface
{
    private EntityRepository $promotionRepository;
    private EntityRepository $ruleRepository;
    private Connection $connection;

    public function __construct(
        EntityRepository $promotionRepository,
        EntityRepository $ruleRepository,
        Connection $connection
    ) {
        $this->promotionRepository = $promotionRepository;
        $this->ruleRepository = $ruleRepository;
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
    $cutoffDate = (new \DateTime())->modify("-{$months} months");

    $criteria = new Criteria();
    $criteria->setLimit(1000);

    // Promotions that expired before now AND before the cutoff
    $criteria->addFilter(new RangeFilter('validUntil', [
        RangeFilter::LT => (new \DateTime())->format(\DATE_ATOM),  // expired
    ]));

    $criteria->addFilter(new RangeFilter('validUntil', [
        RangeFilter::LT => $cutoffDate->format(\DATE_ATOM),        // expired X months ago
    ]));


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

    if (!$dryRun && !empty($sample)) {
        $ids = array_map(fn($p) => ['id' => $p['id']], $sample);
        $this->promotionRepository->delete($ids, $context);
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

    $criteria->addFilter(new RangeFilter('createdAt', [
        RangeFilter::LT => $cutoffDate->format(\DATE_ATOM),
    ]));
    $criteria->addFilter(new EqualsFilter('orderLineItems.id', null)); // uses mapped association
    $criteria->addAssociation('orderLineItems');

    $voucherCodes = $this->promotionRepository->search($criteria, $context); // use injected EntityRepository

    $sample = [];
    foreach ($voucherCodes->getEntities() as $code) {
        $sample[] = [
            'id' => $code->getId(),
            'code' => $code->getCode(),
            'name' => $code->getName(),
            'created_at' => $code->getCreatedAt()?->format('Y-m-d H:i:s'),
        ];
    }
    
    // Perform deletion only if not dryRun and we have something to delete
    if (!$dryRun && !empty($sample)) {
        $ids = array_map(fn($p) => ['id' => $p['id']], $sample);
        $this->promotionRepository->delete($ids, $context);
    }

    return [
        'count' => $voucherCodes->count(),
        'sample' => $sample,
    ];
}

 private function cleanupOrphanedCartRules(bool $dryRun, Context $context): array
{
    $cutoff = (new \DateTime())->modify('-30 days');


    $criteria = new Criteria();
    $criteria->setLimit(100);

    $criteria->addFilter(new RangeFilter('createdAt', [
        RangeFilter::LT => $cutoff->format(\DATE_ATOM),
    ]));
$criteria->addFilter(new MultiFilter('AND', [
    new EqualsFilter('flowSequences.id', null),
    new EqualsFilter('productPrices.id', null),
    new EqualsFilter('shippingMethodPrices.id', null),
    new EqualsFilter('shippingMethods.id', null),
    new EqualsFilter('paymentMethods.id', null),
]));

    $rules = $this->ruleRepository->search($criteria, $context);

    $sample = [];
    foreach ($rules->getEntities() as $rule) {
        $sample[] = [
            'id' => $rule->getId(),
            'name' => $rule->getName(),
            'created_at' => $rule->getCreatedAt()?->format('Y-m-d H:i:s'),
        ];
    }

  if (!$dryRun && $rules->count() > 0) {
    $ids = [];
    foreach ($rules->getEntities() as $entity) {
        $ids[] = ['id' => $entity->getId()];
    }
    $this->ruleRepository->delete($ids, $context);
}
    return [
        'count' => $rules->count(),
        'sample' => $sample
    ];
}



    public function getName(): string
    {
        return 'Promotion & Discount Cleanup';
    }
}
