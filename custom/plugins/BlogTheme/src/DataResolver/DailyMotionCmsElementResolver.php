<?php declare(strict_types=1);

namespace BlogTheme\DataResolver;

use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity;
use Shopware\Core\Content\Cms\DataResolver\Element\AbstractCmsElementResolver;
use Shopware\Core\Content\Cms\DataResolver\Element\ElementDataCollection;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\ResolverContext;
use Shopware\Core\Content\Cms\DataResolver\CriteriaCollection;
use Shopware\Core\Content\Media\MediaEntity;
use Shopware\Core\Content\Media\MediaDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;

class DailyMotionCmsElementResolver extends AbstractCmsElementResolver
{
    
 public function getType(): string
    {
        return 'my-image-text-reversed';
    }

     public function collect(CmsSlotEntity $slot, ResolverContext $resolverContext): ?CriteriaCollection
    {
       $config = $slot->getFieldConfig();
        $myCustomMedia = $config->get('myCustomMedia');

        if (!$myCustomMedia) {
            return null;
        }

        $mediaId = $myCustomMedia->getValue();

        $criteria = new Criteria([$mediaId]);

        $criteriaCollection = new CriteriaCollection();
        $criteriaCollection->add('media_' . $slot->getUniqueIdentifier(), MediaDefinition::class, $criteria);

        return $criteriaCollection;
    }

    public function enrich(CmsSlotEntity $slot, ResolverContext $resolverContext, ElementDataCollection $result): void
    {
        $config = $slot->getFieldConfig();
        $myCustomApiPayload = $config->get('myCustomApiPayload');

        // perform some external api call with the payload `myCustomApiPayload`
        // $myCustomAPI = new MyCustomAPI();

        // $response = $myCustomAPI->query($myCustomApiPayload);

        // if ($response) {
        //     $slot->setData($response);
        // }

    }
}