<?php declare(strict_types=1);

namespace Safebeat\Listener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
use Safebeat\Entity\User;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class JwtSubscriber implements EventSubscriberInterface
{
    public function onJwtCreated(Event $event)
    {
        if (!$event instanceof JWTCreatedEvent) {
            return;
        }

        $user = $event->getUser();
        if (!$user instanceof User) {
            return;
        }

        $event->setData(array_merge($event->getData(), ['user' => $user->jsonSerialize()]));
    }

    public static function getSubscribedEvents()
    {
        return [
            Events::JWT_CREATED => 'onJwtCreated',
        ];
    }
}
