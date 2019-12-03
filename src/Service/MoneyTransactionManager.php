<?php declare(strict_types=1);

namespace Safebeat\Service;

use Doctrine\ORM\EntityManagerInterface;
use Safebeat\Entity\Category;
use Safebeat\Entity\MoneyTransaction;
use Safebeat\Entity\User;
use Safebeat\Entity\Wallet;
use Safebeat\Event\MoneyTransactionEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class MoneyTransactionManager
{
    private EntityManagerInterface $entityManager;
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(EntityManagerInterface $entityManager, EventDispatcherInterface $eventDispatcher)
    {
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function create(User $owner, float $amount, string $description, ?Category $category, ?Wallet $wallet): MoneyTransaction
    {
        $transaction = new MoneyTransaction();

        $transaction->setAmount($amount);
        $transaction->setOwner($owner);
        $transaction->setDescription($description);
        $transaction->setCategory($category);
        $transaction->setWallet($wallet);

        $this->entityManager->persist($transaction);
        $this->entityManager->flush();

        $this->eventDispatcher->dispatch(MoneyTransactionEvent::TRANSACTION_CREATED, new MoneyTransactionEvent($transaction));

        return $transaction;
    }

    public function update(MoneyTransaction $transaction, array $properties): MoneyTransaction
    {
        if (array_key_exists('category', $properties)) {
            $category = $this->entityManager->find(Category::class, $properties['category'] ?? 0);

            if ($properties['category'] !== null && !$category instanceof Category) {
                throw new NotFoundHttpException("Category {$properties['category']} not found!");
            }

            $transaction->setCategory($category);
        }

        if (array_key_exists('wallet', $properties)) {
            $wallet = $this->entityManager->find(Wallet::class, $properties['wallet'] ?? 0);

            if ($properties['wallet'] !== null && !$wallet instanceof Wallet) {
                throw new NotFoundHttpException("Wallet {$properties['wallet']} not found!");
            }

            $transaction->setWallet($wallet);
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
