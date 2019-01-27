<?php declare(strict_types=1);

namespace Safebeat\Repository;

use Doctrine\ORM\EntityRepository;
use Safebeat\Entity\User;

class WalletRepository extends EntityRepository
{
    public function getWalletListByUser(User $user): array
    {
        return $this->findBy(['owner' => $user, 'deleted' => false]);
    }
}
