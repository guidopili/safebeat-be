<?php declare(strict_types=1);

namespace Safebeat\Service\Notification\Notifiers;

use Safebeat\Entity\Notification;
use Safebeat\Entity\User;

interface NotifierInterface
{
    public function notify(User $user, Notification $notification): bool;

    public function isSupportedBy(User $user): bool;
}
