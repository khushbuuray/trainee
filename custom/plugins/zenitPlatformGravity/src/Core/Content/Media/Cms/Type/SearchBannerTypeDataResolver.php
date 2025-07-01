<?php declare(strict_types=1);

namespace zenit\PlatformGravity\Core\Content\Media\Cms\Type;

use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity;
use Shopware\Core\Content\Cms\DataResolver\Element\ElementDataCollection;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\EntityResolverContext;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\ResolverContext;
use Shopware\Core\Content\Cms\SalesChannel\Struct\ImageStruct;
use Shopware\Core\Content\Media\Cms\ImageCmsElementResolver;
use zenit\PlatformGravity\Core\Content\Cms\SalesChannel\Struct\ZenTextImageStruct;

class SearchBannerTypeDataResolver extends ImageCmsElementResolver {

    public function getType(): string {
        return 'zen-search-banner';
    }

    public function enrich(CmsSlotEntity $slot, ResolverContext $resolverContext, ElementDataCollection $result): void
    {
        parent::enrich($slot, $resolverContext, $result);

        /** @var ImageStruct $imageStruct */
        $imageStruct = $slot->getData();
        $textImage = new ZenTextImageStruct();
        $config = $slot->getFieldConfig();

        $content = [];

        $configTextBefore = $config->get('textBefore');
        if ($configTextBefore && $configTextBefore->isStatic()) {
            if ($resolverContext instanceof EntityResolverContext) {
                $content['before'] = (string) $this->resolveEntityValues($resolverContext, $configTextBefore->getStringValue());
            } else {
                $content['before'] = $configTextBefore->getStringValue();
            }
        }

        $configTextAfter = $config->get('textAfter');
        if ($configTextAfter && $configTextAfter->isStatic()) {
            if ($resolverContext instanceof EntityResolverContext) {
                $content['after'] = (string) $this->resolveEntityValues($resolverContext, $configTextAfter->getStringValue());
            } else {
                $content['after'] = $configTextAfter->getStringValue();
            }
        }

        $textImage->setTextBefore($content['before'] ?? '');
        $textImage->setTextAfter($content['after'] ?? '');

        $media = $imageStruct->getMedia();
        if (!empty($media)) {
            $textImage->setMedia($media);
        }

        $mediaId = $imageStruct->getMediaId();
        if (!empty($mediaId)) {
            $textImage->setMediaId($mediaId);
        }

        $slot->setData($textImage);
    }
}
