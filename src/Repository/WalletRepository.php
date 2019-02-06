<?php declare(strict_types=1);

namespace Safebeat\Repository;

use Doctrine\ORM\EntityRepository;
use Safebeat\Entity\User;

class WalletRepository extends EntityRepository
{
    public function getWalletListByUser(User $user): array
    {
        $ownedWallets = $this->findBy(['owner' => $user, 'deleted' => false]);

        $qb = $this->createQueryBuilder('wallet');
        $qb
            ->select('wallet')
            ->distinct()
            ->join('wallet.invitedUsers', 'invited_users')
            ->andWhere('invited_users.id = :user')
            ->setParameter('user', $user);

        return array_merge($ownedWallets, $qb->getQuery()->getResult());
    }
}
