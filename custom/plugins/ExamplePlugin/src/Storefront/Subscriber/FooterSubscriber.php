<?php declare(strict_types=1);

namespace Example\Storefront\Subscriber;

use Shopware\Storefront\Pagelet\Footer\FooterPageletLoadedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class FooterSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            FooterPageletLoadedEvent::class => 'onFooterPageletLoaded',
        ];
    }
    public function onFooterPageletLoaded(FooterPageletLoadedEvent $event): void
    {
        // $event->getPagelet()->addBlock([
        //     'view' => '@Example/Storefront/layout/footer.html.twig',
        // ]);
    }
}