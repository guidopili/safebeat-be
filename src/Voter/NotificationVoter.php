<?php declare(strict_types=1);

namespace Safebeat\Voter;

use Safebeat\Entity\Notification;
use Safebeat\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class NotificationVoter extends Voter
{
    private const ATTRIBUTE_EDIT = 'NOTIFICATION_EDIT';

    private const SUPPORTED_ATTRIBUTES = [
        self::ATTRIBUTE_EDIT,
    ];

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports($attribute, $subject): bool
    {
        return $subject instanceof Notification && in_array($attribute, self::SUPPORTED_ATTRIBUTES, true);
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
            case self::ATTRIBUTE_EDIT:
                return $this->isOwner($subject, $user);
        }

        throw new \LogicException('You shouldn\'t be here');
    }

    private function isOwner(Notification $category, User $user): bool
    {
        return $category->getTargetUser()->getId() === $user->getId();
    }
}
