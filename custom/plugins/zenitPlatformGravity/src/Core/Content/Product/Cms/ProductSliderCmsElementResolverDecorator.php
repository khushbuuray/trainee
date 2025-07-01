<?php declare(strict_types=1);

namespace zenit\PlatformGravity\Core\Content\Product\Cms;

use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity;
use Shopware\Core\Content\Cms\DataResolver\Element\AbstractCmsElementResolver;
use Shopware\Core\Content\Cms\DataResolver\Element\CmsElementResolverInterface;
use Shopware\Core\Content\Cms\DataResolver\Element\ElementDataCollection;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\ResolverContext;
use Shopware\Core\Content\Cms\DataResolver\CriteriaCollection;

class ProductSliderCmsElementResolverDecorator extends AbstractCmsElementResolver
{
    /** @var CmsElementResolverInterface $elementResolver */
    private CmsElementResolverInterface $elementResolver;

    public function getType(): string
    {
        return $this->elementResolver->getType();
    }

    public function __construct(CmsElementResolverInterface $elementResolver)
    {
        $this->elementResolver = $elementResolver;
    }

    public function collect(CmsSlotEntity $slot, ResolverContext $resolverContext): ?CriteriaCollection
    {
        $criteriaCollection = $this->elementResolver->collect($slot, $resolverContext);

        if ($criteriaCollection) {
            foreach ($criteriaCollection as $productCriteria) {
                foreach ($productCriteria as $criteria) {
                    $criteria->addAssociation('media');
                }
            }
        }

        return $criteriaCollection;
    }

    public function enrich(CmsSlotEntity $slot, ResolverContext $resolverContext, ElementDataCollection $result): void
    {
        $this->elementResolver->enrich($slot, $resolverContext, $result);
    }
}
