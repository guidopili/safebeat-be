<?php declare(strict_types=1);

namespace Safebeat\Service;

use Doctrine\ORM\EntityManagerInterface;
use Safebeat\Entity\Category;
use Safebeat\Entity\MoneyTransaction;
use Safebeat\Entity\User;
use Safebeat\Event\MoneyTransactionEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class MoneyTransactionManager
{
    private $entityManager;
    private $eventDispatcher;

    public function __construct(EntityManagerInterface $entityManager, EventDispatcherInterface $eventDispatcher)
    {
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function create(User $owner, float $amount, string $description, ?Category $category): MoneyTransaction
    {
        $transaction = new MoneyTransaction();

        $transaction->setAmount($amount);
        $transaction->setOwner($owner);
        $transaction->setDescription($description);
        $transaction->setCategory($category);

        $this->entityManager->persist($transaction);
        $this->entityManager->flush();

        $this->eventDispatcher->dispatch(MoneyTransactionEvent::TRANSACTION_CREATED, new MoneyTransactionEvent($transaction));

        return $transaction;
    }

    public function update(MoneyTransaction $transaction, array $properties): MoneyTransaction
    {
        if ($category = $this->entityManager->find(Category::class, $properties['category'] ?? 0)) {
            $transaction->setCategory($category);
        }

        $transaction->setAmount($properties['amount'] ?? $transaction->getAmount());
        $transaction->setDescription($properties['description'] ?? $transaction->getDescription());

        $this->entityManager->flush();

        $this->eventDispatcher->dispatch(MoneyTransactionEvent::TRANSACTION_UPDATED, new MoneyTransactionEvent($transaction));

        return $transaction;
    }

    public function delete(MoneyTransaction $transaction): bool
    {
        $eventArgs = ['id' => $transaction->getId()];

        $this->entityManager->remove($transaction);
        $this->entityManager->flush();

        $this->eventDispatcher->dispatch(
            MoneyTransactionEvent::TRANSACTION_DELETED, new MoneyTransactionEvent($transaction, $eventArgs)
        );

        return true;
    }
}
