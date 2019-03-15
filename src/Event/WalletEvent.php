<?php declare(strict_types=1);

namespace Safebeat\Event;

use Safebeat\Entity\Wallet;
use Symfony\Component\EventDispatcher\GenericEvent;

class WalletEvent extends GenericEvent
{
    public const WALLET_CREATED = 'wallet.created';
    public const WALLET_DELETED = 'wallet.deleted';
    public const WALLET_UPDATED = 'wallet.updated';
    public const WALLET_INVITED_USER = 'wallet.invited_user';
    public const WALLET_REMOVED_USER = 'wallet.removed_user';
    public const WALLETD_INVITATION_ACCEPTED = 'wallet.invitation_accepted';

    public function __construct(Wallet $subject, array $arguments = [])
    {
        parent::__construct($subject, $arguments);
    }

    public function getWallet(): Wallet
    {
        return $this->subject;
    }
}
