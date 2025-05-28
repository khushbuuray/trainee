<?php declare(strict_types=1);

namespace zenit\PlatformGravity\Core\Content\Media\Cms\Type;

use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity;
use Shopware\Core\Content\Cms\DataResolver\CriteriaCollection;
use Shopware\Core\Content\Cms\DataResolver\Element\AbstractCmsElementResolver;
use Shopware\Core\Content\Cms\DataResolver\Element\ElementDataCollection;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\EntityResolverContext;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\ResolverContext;
use Shopware\Core\Content\Product\ProductEntity;
use zenit\PlatformGravity\Core\Content\Cms\SalesChannel\Struct\ZenImageSliderItemStruct;
use zenit\PlatformGravity\Core\Content\Cms\SalesChannel\Struct\ZenImageSliderStruct;
use Shopware\Core\Content\Media\Cms\AbstractDefaultMediaResolver;
use Shopware\Core\Content\Media\MediaDefinition;
use Shopware\Core\Content\Media\MediaEntity;
use Shopware\Core\Content\Product\Aggregate\ProductMedia\ProductMediaCollection;
use Shopware\Core\Content\Product\Aggregate\ProductMedia\ProductMediaEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;

class ImageSliderTypeDataResolver extends AbstractCmsElementResolver
{

    private AbstractDefaultMediaResolver $mediaResolver;

    public function __construct(AbstractDefaultMediaResolver $mediaResolver) {
        $this->mediaResolver = $mediaResolver;
    }

    public function getType(): string
    {
        return 'zen-image-slider';
    }

    public function collect(CmsSlotEntity $slot, ResolverContext $resolverContext): ?CriteriaCollection
    {
        $sliderItemsConfig = $slot->getFieldConfig()->get('sliderItems');
        if ($sliderItemsConfig === null || $sliderItemsConfig->isMapped() || $sliderItemsConfig->isDefault()) {
            return null;
        }

        $sliderItems = $sliderItemsConfig->getArrayValue();
        $mediaIds = array_column($sliderItems, 'mediaId');

        $criteria = new Criteria($mediaIds);

        $criteriaCollection = new CriteriaCollection();
        $criteriaCollection->add('media_' . $slot->getUniqueIdentifier(), MediaDefinition::class, $criteria);

        return $criteriaCollection;
    }

    public function enrich(CmsSlotEntity $slot, ResolverContext $resolverContext, ElementDataCollection $result): void
    {
        $config = $slot->getFieldConfig();
        $imageSlider = new ZenImageSliderStruct();
        $slot->setData($imageSlider);

        $navigation = $config->get('navigation');
        if ($navigation !== null && $navigation->isStatic()) {
            $imageSlider->setNavigation($navigation->getArrayValue());
        }

        $sliderItemsConfig = $config->get('sliderItems');
        if ($sliderItemsConfig === null) {
            return;
        }

        if ($sliderItemsConfig->isStatic()) {
            foreach ($sliderItemsConfig->getArrayValue() as $sliderItem) {
                $this->createSliderItemFromConfig($slot, $imageSlider, $result, $sliderItem, $resolverContext);
            }
        }

        if ($sliderItemsConfig->isDefault()) {
            foreach ($sliderItemsConfig->getArrayValue() as $sliderItem) {
                $this->createSliderItemFromDefaults($imageSlider, $sliderItem);
            }
        }

        if ($sliderItemsConfig->isMapped() && $resolverContext instanceof EntityResolverContext) {
            $sliderItems = $this->resolveEntityValue($resolverContext->getEntity(), $sliderItemsConfig->getStringValue());

            if ($sliderItems === null || (is_countable($sliderItems) ? \count($sliderItems) : 0) < 1) {
                return;
            }
            $this->sortItemsByPosition($sliderItems);

            if ($sliderItemsConfig->getStringValue() === 'product.media') {
                /** @var ProductEntity $productEntity */
                $productEntity = $resolverContext->getEntity();

                if ($productEntity->getCover()) {
                    /** @var ProductMediaCollection $sliderItems */
                    $sliderItems = new ProductMediaCollection(array_merge(
                        [$productEntity->getCoverId() => $productEntity->getCover()],
                        $sliderItems->getElements()
                    ));
                }
            }

            foreach ($sliderItems->getMedia() as $media) {
                $imageSliderItem = new ZenImageSliderItemStruct();
                $imageSliderItem->setMedia($media);
                $imageSlider->addSliderItem($imageSliderItem);
            }
        }
    }

    protected function sortItemsByPosition(ProductMediaCollection $sliderItems): void
    {
        if (!$sliderItems->first() || !$sliderItems->first()->has('position')) {
            return;
        }

        $sliderItems->sort(static fn (ProductMediaEntity $a, ProductMediaEntity $b) => $a->get('position') - $b->get('position'));
    }

    private function createSliderItemFromConfig(CmsSlotEntity $slot, ZenImageSliderStruct $imageSlider, ElementDataCollection $result, array $config, ResolverContext $resolverContext): void
    {
        $imageSliderItem = new ZenImageSliderItemStruct();

        if (!empty($config['url'])) {
            $imageSliderItem->setUrl($config['url']);
            $imageSliderItem->setNewTab($config['newTab'] ?? false);
        }

        if (!empty($config['imageBorderRadius'])) {
            $imageSliderItem->setImageBorderRadius($config['imageBorderRadius']);
            $imageSliderItem->setImageBorderRadiusTopLeft($config['imageBorderRadiusTopLeft']);
            $imageSliderItem->setImageBorderRadiusTopRight($config['imageBorderRadiusTopRight']);
            $imageSliderItem->setImageBorderRadiusBottomLeft($config['imageBorderRadiusBottomLeft']);
            $imageSliderItem->setImageBorderRadiusBottomRight($config['imageBorderRadiusBottomRight']);
        }

        if (!empty($config['backgroundBlur'])) {
            $imageSliderItem->setBackgroundBlur($config['backgroundBlur']);
        }

        $imageSliderItem->setHorizontalImageAlign($config['horizontalImageAlign'] ?? 'center');
        $imageSliderItem->setVerticalImageAlign($config['verticalImageAlign'] ?? 'center');
        $imageSliderItem->setHeadlineScale($config['headlineScale'] ?? false);
        $imageSliderItem->setTextScale($config['textScale'] ?? false);

        if (!empty($config['text'])) {
            $configText = $config['text'];

            if ($resolverContext instanceof EntityResolverContext) {
                $resolvedText = (string)$this->resolveEntityValues($resolverContext, $configText);
            } else {
                $resolvedText = $configText;
            }

            $imageSliderItem->setText($resolvedText);
        }

        $imageSliderItem->setColor($config['color']);
        $imageSliderItem->setBackgroundColor($config['backgroundColor']);
        $imageSliderItem->setTextBorderRadius($config['textBorderRadius']);
        $imageSliderItem->setTextBorderRadiusTopLeft($config['textBorderRadiusTopLeft']);
        $imageSliderItem->setTextBorderRadiusTopRight($config['textBorderRadiusTopRight']);
        $imageSliderItem->setTextBorderRadiusBottomRight($config['textBorderRadiusBottomRight']);
        $imageSliderItem->setTextBorderRadiusBottomLeft($config['textBorderRadiusBottomLeft']);
        $imageSliderItem->setTextPaddings($config['textPaddings']);
        $imageSliderItem->setTextPaddingTop($config['textPaddingTop']);
        $imageSliderItem->setTextPaddingRight($config['textPaddingRight']);
        $imageSliderItem->setTextPaddingBottom($config['textPaddingBottom']);
        $imageSliderItem->setTextPaddingLeft($config['textPaddingLeft']);
        $imageSliderItem->setTextMargins($config['textMargins']);
        $imageSliderItem->setTextMarginTop($config['textMarginTop']);
        $imageSliderItem->setTextMarginRight($config['textMarginRight']);
        $imageSliderItem->setTextMarginBottom($config['textMarginBottom']);
        $imageSliderItem->setTextMarginLeft($config['textMarginLeft']);
        $imageSliderItem->setHorizontalTextAlign($config['horizontalTextAlign']);
        $imageSliderItem->setVerticalTextAlign($config['verticalTextAlign']);
        $imageSliderItem->setTextMinWidth($config['textMinWidth']);
        $imageSliderItem->setTextMaxWidth($config['textMaxWidth']);
        $imageSliderItem->setOverlay($config['overlay']);
        $imageSliderItem->setOverlayColor($config['overlayColor']);
        $imageSliderItem->setOverlayOpacity($config['overlayOpacity']);
        $imageSliderItem->setAnimationIn($config['animationIn']);
        $imageSliderItem->setAnimation($config['animation']);
        $imageSliderItem->setCustomItemClasses($config['customItemClasses']);

        $searchResult = $result->get('media_' . $slot->getUniqueIdentifier());
        if (!$searchResult) {
            return;
        }

        /** @var MediaEntity|null $media */
        $media = $searchResult->get($config['mediaId']);
        if (!$media) {
            return;
        }

        $imageSliderItem->setMedia($media);
        $imageSlider->addSliderItem($imageSliderItem);
    }

     private function createSliderItemFromDefaults(ZenImageSliderStruct $imageSlider, array $config): void
     {
        $media = $this->mediaResolver->getDefaultCmsMediaEntity($config['fileName']);

        if ($media === null) {
            return;
        }

        $imageSliderItem = new ZenImageSliderItemStruct();

        if (!empty($config['imageBorderRadius'])) {
            $imageSliderItem->setImageBorderRadius($config['imageBorderRadius']);
            $imageSliderItem->setImageBorderRadiusTopLeft($config['imageBorderRadiusTopLeft']);
            $imageSliderItem->setImageBorderRadiusTopRight($config['imageBorderRadiusTopRight']);
            $imageSliderItem->setImageBorderRadiusBottomLeft($config['imageBorderRadiusBottomLeft']);
            $imageSliderItem->setImageBorderRadiusBottomRight($config['imageBorderRadiusBottomRight']);
        }

         if (!empty($config['backgroundBlur'])) {
             $imageSliderItem->setBackgroundBlur($config['backgroundBlur']);
         }

        $imageSliderItem->setHorizontalImageAlign($config['horizontalImageAlign'] ?? 'center');
        $imageSliderItem->setVerticalImageAlign($config['verticalImageAlign'] ?? 'center');
        $imageSliderItem->setHeadlineScale($config['headlineScale'] ?? false);
        $imageSliderItem->setTextScale($config['textScale'] ?? false);
        $imageSliderItem->setText($config['text']);
        $imageSliderItem->setColor($config['color']);
        $imageSliderItem->setBackgroundColor($config['backgroundColor']);
        $imageSliderItem->setTextBorderRadius($config['textBorderRadius']);
        $imageSliderItem->setTextBorderRadiusTopLeft($config['textBorderRadiusTopLeft']);
        $imageSliderItem->setTextBorderRadiusTopRight($config['textBorderRadiusTopRight']);
        $imageSliderItem->setTextBorderRadiusBottomRight($config['textBorderRadiusBottomRight']);
        $imageSliderItem->setTextBorderRadiusBottomLeft($config['textBorderRadiusBottomLeft']);
        $imageSliderItem->setTextPaddings($config['textPaddings']);
        $imageSliderItem->setTextPaddingTop($config['textPaddingTop']);
        $imageSliderItem->setTextPaddingRight($config['textPaddingRight']);
        $imageSliderItem->setTextPaddingBottom($config['textPaddingBottom']);
        $imageSliderItem->setTextPaddingLeft($config['textPaddingLeft']);
        $imageSliderItem->setTextMargins($config['textMargins']);
        $imageSliderItem->setTextMarginTop($config['textMarginTop']);
        $imageSliderItem->setTextMarginRight($config['textMarginRight']);
        $imageSliderItem->setTextMarginBottom($config['textMarginBottom']);
        $imageSliderItem->setTextMarginLeft($config['textMarginLeft']);
        $imageSliderItem->setHorizontalTextAlign($config['horizontalTextAlign']);
        $imageSliderItem->setVerticalTextAlign($config['verticalTextAlign']);
        $imageSliderItem->setTextMinWidth($config['textMinWidth']);
        $imageSliderItem->setTextMaxWidth($config['textMaxWidth']);
        $imageSliderItem->setOverlay($config['overlay']);
        $imageSliderItem->setOverlayColor($config['overlayColor']);
        $imageSliderItem->setOverlayOpacity($config['overlayOpacity']);
        $imageSliderItem->setAnimationIn($config['animationIn']);
        $imageSliderItem->setAnimation($config['animation']);
        $imageSliderItem->setCustomItemClasses($config['customItemClasses']);
        $imageSliderItem->setMedia($media);
        $imageSlider->addSliderItem($imageSliderItem);
    }
}
