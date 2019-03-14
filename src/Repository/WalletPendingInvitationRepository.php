<?php declare(strict_types=1);

namespace Safebeat\Repository;

use Doctrine\ORM\EntityRepository;
use Safebeat\Entity\User;
use Safebeat\Entity\Wallet;

class WalletPendingInvitationRepository extends EntityRepository
{
    public function existsPendingInvitation(Wallet $wallet, User $user): bool
    {
        return $this->count(['wallet' => $wallet, 'user' => $user]) === 1;
    }
}
