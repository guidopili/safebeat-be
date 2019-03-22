<?php declare(strict_types=1);

namespace Safebeat\Voter;

use Safebeat\Entity\MoneyTransaction;
use Safebeat\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class MoneyTransactionVoter extends Voter
{
    private const ATTRIBUTE_VIEW = 'TRANSACTION_VIEW';
    private const ATTRIBUTE_EDIT = 'TRANSACTION_EDIT';
    private const ATTRIBUTE_DELETE = 'TRANSACTION_DELETE';

    private const SUPPORTED_ATTRIBUTES = [
        self::ATTRIBUTE_VIEW,
        self::ATTRIBUTE_EDIT,
        self::ATTRIBUTE_DELETE,
    ];

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports($attribute, $subject): bool
    {
        return $subject instanceof MoneyTransaction && in_array($attribute, self::SUPPORTED_ATTRIBUTES, true);
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        if ($this->security->isGranted('ROLE_SUPER_ADMIN')) {
            return true;
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

    private function canView(MoneyTransaction $moneyTransaction, User $user): bool
    {
        return $this->isOwner($moneyTransaction, $user) || $moneyTransaction->getWallet()->hasInvitedUser($user);
    }

    private function isOwner(MoneyTransaction $moneyTransaction, User $user): bool
    {
        return $moneyTransaction->getOwner()->getId() === $user->getId();
    }
}
