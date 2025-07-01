<?php declare(strict_types=1);

namespace zenit\PlatformGravity\Subscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class KernelControllerSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController'
        ];
    }

    public function onKernelController(ControllerEvent $event): void {
        $attributes = $event->getRequest()->attributes;

        //allow XHR for add-by-number to make AJAX work in offcanvas
        if ($attributes->get('_route') === 'frontend.checkout.product.add-by-number') {
            $attributes->set('XmlHttpRequest', true);
        }
    }
}