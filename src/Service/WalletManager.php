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
}
