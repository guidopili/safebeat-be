<?php declare(strict_types=1);

namespace Safebeat\Repository;

use Doctrine\ORM\EntityRepository;
use Safebeat\Entity\User;
use Safebeat\Entity\Wallet;

class MoneyTransactionRepository extends EntityRepository
{
    public function getTransactionsByOwner(User $user): array
    {
        return $this->findBy(['owner' => $user]);
    }

    public function getTransactionsListByWallet(Wallet $wallet): array
    {
        return $this->findBy(['wallet' => $wallet]);
    }
}
