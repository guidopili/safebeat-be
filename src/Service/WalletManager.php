<?php declare(strict_types=1);

namespace Safebeat\Service;

use Doctrine\ORM\EntityManagerInterface;
use Safebeat\Entity\User;
use Safebeat\Entity\Wallet;
use Safebeat\Entity\WalletPendingInvitation;
use Safebeat\Event\WalletEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class WalletManager
{
    private $entityManager;
    private $eventDispatcher;

    public function __construct(EntityManagerInterface $entityManager, EventDispatcherInterface $eventDispatcher)
    {
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function create(string $title, User $owner): Wallet
    {
        $wallet = new Wallet();

        $wallet->setTitle($title);
        $wallet->setOwner($owner);

        $this->entityManager->persist($wallet);
        $this->entityManager->flush();

        $this->eventDispatcher->dispatch(WalletEvent::WALLET_CREATED, new WalletEvent($wallet));

        return $wallet;
    }

    public function update(Wallet $wallet, array $properties): Wallet
    {
        if (array_key_exists('owner', $properties)) {
            $newOwner = $this->entityManager->find(User::class, $properties['owner'] ?? 0);

            if ($newOwner instanceof User || $properties['owner'] === null) {
                $wallet->setOwner($newOwner);
            }
        }

        $wallet->setTitle($properties['title'] ?? $wallet->getTitle());

        $this->entityManager->flush();

        $this->eventDispatcher->dispatch(WalletEvent::WALLET_UPDATED, new WalletEvent($wallet));

        return $wallet;
    }

    public function delete(Wallet $wallet): bool
    {
        $wallet->setDeleted(true);

        $this->entityManager->persist($wallet);
        $this->entityManager->flush();

        $this->eventDispatcher->dispatch(WalletEvent::WALLET_DELETED, new WalletEvent($wallet));

        return true;
    }

    public function inviteUsers(Wallet $wallet, User $user): bool
    {
        if ($wallet->getOwner()->getId() === $user->getId()) {
            return false;
        }

        if ($wallet->getInvitedUsers()->contains($user)) {
            return false;
        }

        $pendingRepository = $this->entityManager->getRepository(WalletPendingInvitation::class);
        if (true === $pendingRepository->existsPendingInvitation($wallet, $user)) {
            return false;
        }

        $walletPendingInvitation = new WalletPendingInvitation($wallet, $user);

        $this->entityManager->persist($walletPendingInvitation);
        $this->entityManager->flush();

        $this->eventDispatcher->dispatch(
            WalletEvent::WALLET_INVITED_USER,
            new WalletEvent($wallet, ['invitedUser' => $user])
        );

        return true;
    }
}
