<?php declare(strict_types=1);

namespace Safebeat\Listener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
use Safebeat\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\EventDispatcher\Event;

class JwtSubscriber implements EventSubscriberInterface
{
    public function onJwtCreated(Event $event): void
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

    public static function getSubscribedEvents(): array
    {
        return [
            Events::JWT_CREATED => 'onJwtCreated',
        ];
    }
}
