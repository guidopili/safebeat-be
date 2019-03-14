<?php declare(strict_types=1);

namespace Safebeat\Listener;

use Safebeat\Entity\Notification;
use Safebeat\Entity\User;
use Safebeat\Event\WalletEvent;
use Safebeat\Service\Notification\NotificationService;

class WalletListener
{
    private $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function onWalletInvitedUser(WalletEvent $event)
    {
        if (false === $event->hasArgument('invitedUser')) {
            return;
        }

        $user = $event->getArgument('invitedUser');

        if (!$user instanceof User) {
            return;
        }

        $wallet = $event->getWallet();

        $notification = new Notification();

        $notification->setTitle('New invitation to wallet');
        $notification->setContent("You were invited to {$wallet->getTitle()} by {$wallet->getOwner()->getFullName()}");

        $this->notificationService->sendNotificationToUser($user, $notification);
    }
}
