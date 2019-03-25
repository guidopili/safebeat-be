<?php declare(strict_types=1);

namespace Safebeat\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Safebeat\Event\WalletEvent;
use Safebeat\Entity\{User, Wallet, WalletPendingInvitation};

class WalletInvitationManager
{
    private $entityManager;
    private $eventDispatcher;
    private $pendingInvitationRepository;

    public function __construct(EntityManagerInterface $entityManager, EventDispatcherInterface $eventDispatcher)
    {
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->pendingInvitationRepository = $entityManager->getRepository(WalletPendingInvitation::class);
    }

    public function inviteUser(Wallet $wallet, User $user): bool
    {
        if ($wallet->getOwner()->getId() === $user->getId() || $wallet->getInvitedUsers()->contains($user)) {
            return false;
        }

        if (true === $this->pendingInvitationRepository->existsPendingInvitation($wallet, $user)) {
            return false;
        }

        $this->entityManager->beginTransaction();
        try {
            $walletPendingInvitation = new WalletPendingInvitation($wallet, $user);

            $this->entityManager->persist($walletPendingInvitation);
            $this->entityManager->flush();

            $this->eventDispatcher->dispatch(
                WalletEvent::WALLET_INVITED_USER,
                new WalletEvent($wallet, ['invitedUser' => $user])
            );
        } catch (\Throwable $e) {
            $this->entityManager->rollback();
            $this->entityManager->close();

            throw $e;
        }

        return true;
    }

    public function removeInvitedUser(Wallet $wallet, User $user): bool
    {
        if ($wallet->getOwner()->getId() === $user->getId()) {
            return false;
        }

        $pendingInvitation = $this->getPendingInvitation($wallet, $user);
        if ($pendingInvitation instanceof WalletPendingInvitation) {
            $this->entityManager->beginTransaction();
            try {
                $this->entityManager->remove($pendingInvitation);
                $this->entityManager->flush();

                $this->eventDispatcher->dispatch(
                    WalletEvent::WALLET_REMOVED_USER,
                    new WalletEvent(
                        $wallet, [
                            'removedUser' => $user,
                            'notificationTitle' => 'Invitation to wallet revoked',
                            'notificationContent' => "Your invitation to $wallet have been revoked by {$wallet->getOwner()}",
                        ]
                    )
                );
            } catch (\Throwable $e) {
                $this->entityManager->rollback();
                $this->entityManager->close();

                throw $e;
            }

            return true;
        }

        if (!$wallet->getInvitedUsers()->contains($user)) {
            return false;
        }

        $this->entityManager->beginTransaction();
        try {

            $wallet->removeInvitedUser($user);
            $this->entityManager->flush();

            $this->eventDispatcher->dispatch(
                WalletEvent::WALLET_REMOVED_USER,
                new WalletEvent(
                    $wallet, [
                        'removedUser' => $user,
                        'notificationTitle' => 'Removed from wallet',
                        'notificationContent' => "You were removed from {$wallet->getTitle()} by {$wallet->getOwner()}",
                    ]
                )
            );

        } catch (\Throwable $e) {
            $this->entityManager->rollback();
            $this->entityManager->close();

            throw $e;
        }

        return true;
    }

    public function getPendingInvitation(Wallet $wallet, User $user): ?WalletPendingInvitation
    {
        return $this->pendingInvitationRepository->findOneBy(['wallet' => $wallet, 'user' => $user]);
    }
}
