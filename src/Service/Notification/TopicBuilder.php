<?php declare(strict_types=1);

namespace Safebeat\Service\Notification;

use Safebeat\Entity\User;

class TopicBuilder
{
    private const TOPIC_FORMAT = 'http://safebeat/%s/%d';

    public static function buildTopicForUser(User $user): string
    {
        return sprintf(self::TOPIC_FORMAT, 'user', $user->getId());
    }
}
