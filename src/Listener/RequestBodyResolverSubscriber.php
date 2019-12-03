<?php declare(strict_types=1);

namespace Safebeat\Listener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class RequestBodyResolverSubscriber implements EventSubscriberInterface
{
    public function onRequest(RequestEvent $event)
    {
        if (false === \in_array($event->getRequest()->getMethod(), ['POST', 'PUT', 'PATCH'], true)) {
            return;
        }

        $parameters = \json_decode($event->getRequest()->getContent(), true);

        if (empty($parameters)) {
            return;
        }

        $event->getRequest()->request = new ParameterBag($parameters);
    }

    public static function getSubscribedEvents()
    {
        return [KernelEvents::REQUEST => 'onRequest'];
    }
}
