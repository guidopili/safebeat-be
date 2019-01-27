<?php declare(strict_types=1);

namespace Safebeat\Service;

use Doctrine\ORM\EntityManagerInterface;
use Safebeat\Entity\User;
use Safebeat\Entity\Wallet;
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
        if (isset($properties['owner'])) {
            $newOwner =
                $properties['owner'] instanceof User
                    ? $properties['owner']
                    : $this->entityManager->find(User::class, $properties['owner']);

            if ($newOwner instanceof User) {
                $wallet->setOwner($newOwner);
            }
        }

        if (isset($properties['title'])) {
            $wallet->setTitle($properties['title']);
        }

        try {
            $this->entityManager->flush();
        }catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            throw $e;
        }

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
}
