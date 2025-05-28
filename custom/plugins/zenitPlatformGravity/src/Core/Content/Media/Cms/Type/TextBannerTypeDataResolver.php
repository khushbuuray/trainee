<?php declare(strict_types=1);

namespace zenit\PlatformGravity\Core\Content\Media\Cms\Type;

use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity;
use Shopware\Core\Content\Cms\DataResolver\Element\ElementDataCollection;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\EntityResolverContext;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\ResolverContext;
use Shopware\Core\Content\Cms\SalesChannel\Struct\ImageStruct;
use Shopware\Core\Content\Media\Cms\ImageCmsElementResolver;
use zenit\PlatformGravity\Core\Content\Cms\SalesChannel\Struct\ZenTextImageStruct;

class TextBannerTypeDataResolver extends ImageCmsElementResolver {

    public function getType(): string {
        return 'zen-text-banner';
    }

    public function enrich(CmsSlotEntity $slot, ResolverContext $resolverContext, ElementDataCollection $result): void
    {
        parent::enrich($slot, $resolverContext, $result);

        /** @var ImageStruct $imageStruct */
        $imageStruct = $slot->getData();
        $textImage = new ZenTextImageStruct();
        $config = $slot->getFieldConfig();

        $configText = $config->get('text');
        if ($configText !== null) {
            $content = null;

            if ($configText->isStatic()) {
                if ($resolverContext instanceof EntityResolverContext) {
                    $content = (string) $this->resolveEntityValues($resolverContext, $configText->getStringValue());
                } else {
                    $content = $configText->getStringValue();
                }
            }

            $textImage->setContent($content);
        }

        $textImage->setUrl($imageStruct->getUrl());
        $textImage->setNewTab($imageStruct->getNewTab());

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
