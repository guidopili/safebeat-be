<?php declare(strict_types=1);

namespace Safebeat\Voter;

use Safebeat\Entity\Notification;
use Safebeat\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class NotificationVoter extends AbstractVoter
{
    private const ATTRIBUTE_EDIT = 'NOTIFICATION_EDIT';

    private const SUPPORTED_ATTRIBUTES = [
        self::ATTRIBUTE_EDIT,
    ];

    protected function supports($attribute, $subject): bool
    {
        return $subject instanceof Notification && in_array($attribute, self::SUPPORTED_ATTRIBUTES, true);
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        [$user, $ret] = $this->commonChecks($token);

        if (null !== $ret) {
            return $ret;
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
