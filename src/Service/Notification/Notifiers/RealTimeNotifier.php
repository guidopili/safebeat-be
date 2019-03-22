<?php declare(strict_types=1);

namespace Safebeat\Service\Notification\Notifiers;

use Safebeat\Entity\Notification;
use Safebeat\Entity\User;
use Safebeat\Service\Notification\TopicBuilder;
use Symfony\Component\Mercure\Publisher;
use Symfony\Component\Mercure\Update;

class RealTimeNotifier implements NotifierInterface
{
    private $publisher;

    public function __construct(Publisher $publisher)
    {
        $this->publisher = $publisher;
    }

    public function notify(User $user, Notification $notification): bool
    {
        $this->publisher->__invoke(
            new Update(
                TopicBuilder::buildTopicForUser($user),
                json_encode($notification->jsonSerialize())
            )
        );

        return true;
    }

    public static function isSupportedBy(User $user): bool
    {
        return true;
    }
}
