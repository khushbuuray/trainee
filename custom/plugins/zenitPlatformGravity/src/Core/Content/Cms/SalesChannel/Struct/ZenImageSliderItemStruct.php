<?php declare(strict_types=1);

namespace zenit\PlatformGravity\Core\Content\Cms\SalesChannel\Struct;

use Shopware\Core\Content\Media\MediaEntity;
use Shopware\Core\Framework\Struct\Struct;

class ZenImageSliderItemStruct extends Struct
{
    /**
     * @var string|null
     */
    protected $url;

    /**
     * @var bool|null
     */
    protected $newTab;

    /**
     * @var MediaEntity|null
     */
    protected $media;

    /**
     * @var string|null
     */
    protected $text;

    /**
     * @var bool|null
     */
    protected $headlineScale;

    /**
     * @var bool|null
     */
    protected $textScale;

    /**
     * @var string|null
     */
    protected $color;

    /**
     * @var string|null
     */
    protected $backgroundColor;

    /**
     * @var string|null
     */
    protected $backgroundBlur;

    /**
     * @var bool|null
     */
    protected $textBorderRadius;

    /**
     * @var string|null
     */
    protected $textBorderRadiusTopLeft;

    /**
     * @var string|null
     */
    protected $textBorderRadiusTopRight;

    /**
     * @var string|null
     */
    protected $textBorderRadiusBottomRight;

    /**
     * @var string|null
     */
    protected $textBorderRadiusBottomLeft;

    /**
     * @var bool|null
     */
    protected $textPaddings;

    /**
     * @var string|null
     */
    protected $textPaddingTop;

    /**
     * @var string|null
     */
    protected $textPaddingRight;

    /**
     * @var string|null
     */
    protected $textPaddingBottom;

    /**
     * @var string|null
     */
    protected $textPaddingLeft;

    /**
     * @var bool|null
     */
    protected $textMargins;

    /**
     * @var string|null
     */
    protected $textMarginTop;

    /**
     * @var string|null
     */
    protected $textMarginRight;

    /**
     * @var string|null
     */
    protected $textMarginBottom;

    /**
     * @var string|null
     */
    protected $textMarginLeft;

    /**
     * @var string|null
     */
    protected $horizontalTextAlign;

    /**
     * @var string|null
     */
    protected $verticalTextAlign;

    /**
     * @var string|null
     */
    protected $textMinWidth;

    /**
     * @var string|null
     */
    protected $textMaxWidth;

    /**
     * @var string|null
     */
    protected $horizontalImageAlign;

    /**
     * @var string|null
     */
    protected $verticalImageAlign;

    /**
     * @var string|null
     */
    protected $horizontalImageTextAlign;

    /**
     * @var string|null
     */
    protected $verticalImageTextAlign;

    /**
     * @var bool|null
     */
    protected $overlay;

    /**
     * @var string|null
     */
    protected $overlayColor;

    /**
     * @var string|null
     */
    protected $overlayOpacity;

    /**
     * @var bool|null
     */
    protected $imageBorderRadius;

    /**
     * @var string|null
     */
    protected $imageBorderRadiusTopLeft;

    /**
     * @var string|null
     */
    protected $imageBorderRadiusTopRight;

    /**
     * @var string|null
     */
    protected $imageBorderRadiusBottomLeft;

    /**
     * @var string|null
     */
    protected $imageBorderRadiusBottomRight;

    /**
     * @var string|null
     */
    protected $animationIn;

    /**
     * @var string|null
     */
    protected $animation;

    /**
     * @var string|null
     */
    protected $customItemClasses;

    public function getMedia(): ?MediaEntity
    {
        return $this->media;
    }

    public function setMedia(?MediaEntity $media): void
    {
        $this->media = $media;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): void
    {
        $this->url = $url;
    }

    public function getNewTab(): ?bool
    {
        return $this->newTab;
    }

    public function setNewTab(?bool $newTab): void
    {
        $this->newTab = $newTab;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(?string $text): void
    {
        $this->text = $text;
    }

    public function getHeadlineScale(): ?bool
    {
        return $this->headlineScale;
    }

    public function setHeadlineScale(?bool $headlineScale): void
    {
        $this->headlineScale = $headlineScale;
    }

    public function getTextScale(): ?bool
    {
        return $this->textScale;
    }

    public function setTextScale(?bool $textScale): void
    {
        $this->textScale = $textScale;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }
    
    public function setColor(?string $color): void
    {
       $this->color = $color;
    }
    
    public function getBackgroundColor(): ?string
    {
        return $this->backgroundColor;
    }
    
    public function setBackgroundColor(?string $backgroundColor): void
    {
       $this->backgroundColor = $backgroundColor;
    }

    public function getBackgroundBlur(): ?string
    {
        return $this->backgroundBlur;
    }

    public function setBackgroundBlur(?string $backgroundBlur): void
    {
        $this->backgroundBlur = $backgroundBlur;
    }
    
    public function getTextBorderRadius(): ?bool
    {
        return $this->textBorderRadius;
    }
    
    public function setTextBorderRadius(?bool $textBorderRadius): void
    {
       $this->textBorderRadius = $textBorderRadius;
    }
    
    public function getTextBorderRadiusTopLeft(): ?string
    {
        return $this->textBorderRadiusTopLeft;
    }
    
    public function setTextBorderRadiusTopLeft(?string $textBorderRadiusTopLeft): void
    {
       $this->textBorderRadiusTopLeft = $textBorderRadiusTopLeft;
    }
    
    public function getTextBorderRadiusTopRight(): ?string
    {
        return $this->textBorderRadiusTopRight;
    }
    
    public function setTextBorderRadiusTopRight(?string $textBorderRadiusTopRight): void
    {
       $this->textBorderRadiusTopRight = $textBorderRadiusTopRight;
    }
    
    public function getTextBorderRadiusBottomRight(): ?string
    {
        return $this->textBorderRadiusBottomRight;
    }
    
    public function setTextBorderRadiusBottomRight(?string $textBorderRadiusBottomRight): void
    {
       $this->textBorderRadiusBottomRight = $textBorderRadiusBottomRight;
    }
    
    public function getTextBorderRadiusBottomLeft(): ?string
    {
        return $this->textBorderRadiusBottomLeft;
    }
    
    public function setTextBorderRadiusBottomLeft(?string $textBorderRadiusBottomLeft): void
    {
       $this->textBorderRadiusBottomLeft = $textBorderRadiusBottomLeft;
    }
    
    public function getTextPaddings(): ?bool
    {
        return $this->textPaddings;
    }
    
    public function setTextPaddings(?bool $textPaddings): void
    {
       $this->textPaddings = $textPaddings;
    }
    
    public function getTextPaddingTop(): ?string
    {
        return $this->textPaddingTop;
    }
    
    public function setTextPaddingTop(?string $textPaddingTop): void
    {
       $this->textPaddingTop = $textPaddingTop;
    }
    
    public function getTextPaddingRight(): ?string
    {
        return $this->textPaddingRight;
    }
    
    public function setTextPaddingRight(?string $textPaddingRight): void
    {
       $this->textPaddingRight = $textPaddingRight;
    }
    
    public function getTextPaddingBottom(): ?string
    {
        return $this->textPaddingBottom;
    }
    
    public function setTextPaddingBottom(?string $textPaddingBottom): void
    {
       $this->textPaddingBottom = $textPaddingBottom;
    }
    
    public function getTextPaddingLeft(): ?string
    {
        return $this->textPaddingLeft;
    }
    
    public function setTextPaddingLeft(?string $textPaddingLeft): void
    {
       $this->textPaddingLeft = $textPaddingLeft;
    }
    
    public function getTextMargins(): ?bool
    {
        return $this->textMargins;
    }
    
    public function setTextMargins(?bool $textMargins): void
    {
       $this->textMargins = $textMargins;
    }
    
    public function getTextMarginTop(): ?string
    {
        return $this->textMarginTop;
    }
    
    public function setTextMarginTop(?string $textMarginTop): void
    {
       $this->textMarginTop = $textMarginTop;
    }
    
    public function getTextMarginRight(): ?string
    {
        return $this->textMarginRight;
    }
    
    public function setTextMarginRight(?string $textMarginRight): void
    {
       $this->textMarginRight = $textMarginRight;
    }
    
    public function getTextMarginBottom(): ?string
    {
        return $this->textMarginBottom;
    }
    
    public function setTextMarginBottom(?string $textMarginBottom): void
    {
       $this->textMarginBottom = $textMarginBottom;
    }
    
    public function getTextMarginLeft(): ?string
    {
        return $this->textMarginLeft;
    }
    
    public function setTextMarginLeft(?string $textMarginLeft): void
    {
       $this->textMarginLeft = $textMarginLeft;
    }

    public function getHorizontalTextAlign(): ?string
    {
        return $this->horizontalTextAlign;
    }

    public function setHorizontalTextAlign(?string $horizontalTextAlign): void
    {
        $this->horizontalTextAlign = $horizontalTextAlign;
    }

    public function getVerticalTextAlign(): ?string
    {
        return $this->verticalTextAlign;
    }
    
    public function setVerticalTextAlign(?string $verticalTextAlign): void
    {
       $this->verticalTextAlign = $verticalTextAlign;
    }
    
    public function getTextMinWidth(): ?string
    {
        return $this->textMinWidth;
    }
    
    public function setTextMinWidth(?string $textMinWidth): void
    {
       $this->textMinWidth = $textMinWidth;
    }
    
    public function getTextMaxWidth(): ?string
    {
        return $this->textMaxWidth;
    }
    
    public function setTextMaxWidth(?string $textMaxWidth): void
    {
       $this->textMaxWidth = $textMaxWidth;
    }

    public function getHorizontalImageAlign(): ?string
    {
        return $this->horizontalImageAlign;
    }

    public function setHorizontalImageAlign(?string $horizontalImageAlign): void
    {
        $this->horizontalImageAlign = $horizontalImageAlign;
    }

    public function getVerticalImageAlign(): ?string
    {
        return $this->verticalImageAlign;
    }

    public function setVerticalImageAlign(?string $verticalImageAlign): void
    {
        $this->verticalImageAlign = $verticalImageAlign;
    }
    
    public function getOverlay(): ?bool
    {
        return $this->overlay;
    }
    
    public function setOverlay(?bool $overlay): void
    {
       $this->overlay = $overlay;
    }
    
    public function getOverlayColor(): ?string
    {
        return $this->overlayColor;
    }
    
    public function setOverlayColor(?string $overlayColor): void
    {
       $this->overlayColor = $overlayColor;
    }
    
    public function getOverlayOpacity(): ?string
    {
        return $this->overlayOpacity;
    }
    
    public function setOverlayOpacity(?string $overlayOpacity): void
    {
       $this->overlayOpacity = $overlayOpacity;
    }

    public function getImageBorderRadius(): ?bool
    {
        return $this->imageBorderRadius;
    }

    public function setImageBorderRadius(?bool $imageBorderRadius): void
    {
        $this->imageBorderRadius = $imageBorderRadius;
    }

    public function getImageBorderRadiusTopLeft(): ?string
    {
        return $this->imageBorderRadiusTopLeft;
    }

    public function setImageBorderRadiusTopLeft(?string $imageBorderRadiusTopLeft): void
    {
        $this->imageBorderRadiusTopLeft = $imageBorderRadiusTopLeft;
    }

    public function getImageBorderRadiusTopRight(): ?string
    {
        return $this->imageBorderRadiusTopRight;
    }

    public function setImageBorderRadiusTopRight(?string $imageBorderRadiusTopRight): void
    {
        $this->imageBorderRadiusTopRight = $imageBorderRadiusTopRight;
    }

    public function getImageBorderRadiusBottomLeft(): ?string
    {
        return $this->imageBorderRadiusBottomLeft;
    }

    public function setImageBorderRadiusBottomLeft(?string $imageBorderRadiusBottomLeft): void
    {
        $this->imageBorderRadiusBottomLeft = $imageBorderRadiusBottomLeft;
    }

    public function getImageBorderRadiusBottomRight(): ?string
    {
        return $this->imageBorderRadiusBottomRight;
    }

    public function setImageBorderRadiusBottomRight(?string $imageBorderRadiusBottomRight): void
    {
        $this->imageBorderRadiusBottomRight = $imageBorderRadiusBottomRight;
    }

    public function getAnimationIn(): ?string
    {
        return $this->animationIn;
    }

    public function setAnimationIn(?string $animationIn): void
    {
        $this->animationIn = $animationIn;
    }

    public function getAnimation(): ?string
    {
        return $this->animation;
    }
    
    public function setAnimation(?string $animation): void
    {
       $this->animation = $animation;
    }
    
    public function getCustomItemClasses(): ?string
    {
        return $this->customItemClasses;
    }
    
    public function setCustomItemClasses(?string $customItemClasses): void
    {
       $this->customItemClasses = $customItemClasses;
    }

    public function getApiAlias(): string
    {
        return 'cms_zen_image_slider_item';
    }
}
