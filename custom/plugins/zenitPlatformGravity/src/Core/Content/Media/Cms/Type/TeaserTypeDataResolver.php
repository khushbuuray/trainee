<?php declare(strict_types=1);

namespace zenit\PlatformGravity\Core\Content\Media\Cms\Type;

use Shopware\Core\Content\Media\Cms\ImageCmsElementResolver;

class TeaserTypeDataResolver extends ImageCmsElementResolver {
    public function getType(): string {
        return 'zen-teaser';
    }
}
