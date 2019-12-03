<?php declare(strict_types=1);

namespace Safebeat\Voter;

use Safebeat\Entity\User;
use Safebeat\Entity\Wallet;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class WalletVoter extends AbstractVoter
{
    private const ATTRIBUTE_VIEW = 'WALLET_VIEW';
    private const ATTRIBUTE_EDIT = 'WALLET_EDIT';
    private const ATTRIBUTE_DELETE = 'WALLET_DELETE';

    private const SUPPORTED_ATTRIBUTES = [
        self::ATTRIBUTE_VIEW,
        self::ATTRIBUTE_EDIT,
        self::ATTRIBUTE_DELETE,
    ];

    protected function supports($attribute, $subject): bool
    {
        return $subject instanceof Wallet && in_array($attribute, self::SUPPORTED_ATTRIBUTES, true);
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        [$user, $ret] = $this->commonChecks($token);

        if (null !== $ret) {
            return $ret;
        }

        switch ($attribute) {
            case self::ATTRIBUTE_VIEW:
                return $this->canView($subject, $user);
            case self::ATTRIBUTE_EDIT:
            case self::ATTRIBUTE_DELETE:
                return $this->isOwner($subject, $user);
        }

        throw new \LogicException('You shouldn\'t be here');
    }

    private function canView(Wallet $wallet, User $user): bool
    {
        return $this->isOwner($wallet, $user) || $wallet->hasInvitedUser($user);
    }

    private function isOwner(Wallet $wallet, User $user): bool
    {
        return $wallet->getOwner()->getId() === $user->getId();
    }
}
