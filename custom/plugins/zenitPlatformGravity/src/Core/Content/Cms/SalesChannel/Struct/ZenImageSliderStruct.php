<?php declare(strict_types=1);

namespace zenit\PlatformGravity\Core\Content\Cms\SalesChannel\Struct;

use Shopware\Core\Framework\Struct\Struct;

class ZenImageSliderStruct extends Struct
{
    /**
     * @var array|null
     */
    protected $navigation;

    /**
     * @var ZenImageSliderItemStruct[]|null
     */
    protected $sliderItems = [];

    /**
     * @return ZenImageSliderItemStruct[]|null
     */
    public function getSliderItems(): ?array
    {
        return $this->sliderItems;
    }

    /**
     * @param ZenImageSliderItemStruct[]|null $sliderItems
     */
    public function setSliderItems(?array $sliderItems): void
    {
        $this->sliderItems = $sliderItems;
    }

    public function addSliderItem(ZenImageSliderItemStruct $sliderItem): void
    {
        $this->sliderItems[] = $sliderItem;
    }

    public function getNavigation(): ?array
    {
        return $this->navigation;
    }

    public function setNavigation(?array $navigation): void
    {
        $this->navigation = $navigation;
    }

    public function getApiAlias(): string
    {
        return 'cms_zen_image_slider';
    }
}
