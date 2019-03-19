<?php declare(strict_types=1);

namespace Safebeat\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Safebeat\Repository\WalletPendingInvitationRepository")
 * @ORM\Table("wallet_pending_invitation", uniqueConstraints={
 *    @ORM\UniqueConstraint(columns={
 *       "wallet_id",
 *       "user_id"
 *    }),
 * })
 */
class WalletPendingInvitation extends BaseEntity
{
    use TimeStampable;

    /**
     * @var Wallet
     * @ORM\ManyToOne(targetEntity="Wallet")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $wallet;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $user;

    public function __construct(Wallet $wallet, User $user)
    {
        $this->wallet = $wallet;
        $this->user = $user;
    }

    public function getWallet(): Wallet
    {
        return $this->wallet;
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
