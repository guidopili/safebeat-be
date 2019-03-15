<?php declare(strict_types=1);

namespace Safebeat\Listener;

use Safebeat\Entity\Notification;
use Safebeat\Entity\User;
use Safebeat\Event\WalletEvent;
use Safebeat\Service\Notification\NotificationService;
use Symfony\Component\Routing\RouterInterface;

class WalletListener
{
    private $notificationService;
    private $router;

    public function __construct(NotificationService $notificationService, RouterInterface $router)
    {
        $this->notificationService = $notificationService;
        $this->router = $router;
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
        $notification->setContent("You were invited to $wallet by {$wallet->getOwner()}");

        $notification->addLink(
            $this->router->generate('wallet_accept_invitation', ['wallet' => $wallet->getId()]),
            'success'
        );
        $notification->addLink(
            $this->router->generate('wallet_decline_invitation', ['wallet' => $wallet->getId()]),
            'failure'
        );

        $this->notificationService->sendNotificationToUser($user, $notification);
    }

    public function onWalletRemovedUser(WalletEvent $event)
    {
        $requiredKeys = ['removedUser', 'notificationTitle', 'notificationContent'];
        if (!empty(array_diff($requiredKeys, array_keys($event->getArguments())))) {
            return;
        }

        $user = $event->getArgument('removedUser');

        if (!$user instanceof User) {
            return;
        }

        $notification = new Notification();

        $notification->setTitle($event->getArgument('notificationTitle'));
        $notification->setContent($event->getArgument('notificationContent'));

        $this->notificationService->sendNotificationToUser($user, $notification);
    }
}
