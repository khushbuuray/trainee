<?php declare(strict_types=1);

namespace zenit\PlatformGravity\Subscriber;

use Shopware\Core\Framework\Struct\ArrayEntity;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Shopware\Storefront\Event\StorefrontRenderEvent;

class StorefrontRenderSubscriber implements EventSubscriberInterface
{

    /**
     * @var string
     */
    private $pluginName = 'zenitPlatformGravity';

    public static function getSubscribedEvents(): array
    {
        return [
            StorefrontRenderEvent::class => 'onStorefrontRender',
        ];
    }

    public function onStorefrontRender(StorefrontRenderEvent $event)
    {
        $config = [];
        $config['system'] = 'platform';
        $event->getContext()->addExtension($this->pluginName, new ArrayEntity($config));
    }
}
