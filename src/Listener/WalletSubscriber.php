<?php declare(strict_types=1);

namespace Safebeat\Listener;

use Safebeat\Entity\Notification;
use Safebeat\Entity\User;
use Safebeat\Event\WalletEvent;
use Safebeat\Service\Notification\NotificationService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\RouterInterface;

class WalletSubscriber implements EventSubscriberInterface
{
    private NotificationService $notificationService;
    private RouterInterface $router;

    public function __construct(NotificationService $notificationService, RouterInterface $router)
    {
        $this->notificationService = $notificationService;
        $this->router = $router;
    }

    public function onWalletInvitedUser(WalletEvent $event): void
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
        $notification->setTargetUser($user);

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

    public function onWalletRemovedUser(WalletEvent $event): void
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
        $notification->setTargetUser($user);

        $this->notificationService->sendNotificationToUser($user, $notification);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            WalletEvent::WALLET_REMOVED_USER => 'onWalletRemovedUser',
            WalletEvent::WALLET_INVITED_USER => 'onWalletInvitedUser'
        ];
    }
}
