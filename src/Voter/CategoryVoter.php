<?php declare(strict_types=1);

namespace Safebeat\Voter;

use Safebeat\Entity\Category;
use Safebeat\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class CategoryVoter extends AbstractVoter
{
    private const ATTRIBUTE_VIEW = 'CATEGORY_VIEW';
    private const ATTRIBUTE_DELETE = 'CATEGORY_DELETE';

    private const SUPPORTED_ATTRIBUTES = [
        self::ATTRIBUTE_VIEW,
        self::ATTRIBUTE_DELETE,
    ];

    protected function supports($attribute, $subject): bool
    {
        return $subject instanceof Category && in_array($attribute, self::SUPPORTED_ATTRIBUTES, true);
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        [$user, $ret] = $this->commonChecks($token);

        if (null !== $ret) {
            return $ret;
        }

        switch ($attribute) {
            case self::ATTRIBUTE_VIEW:
            case self::ATTRIBUTE_DELETE:
                return $this->isOwner($subject, $user);
        }

        throw new \LogicException('You shouldn\'t be here');
    }

    private function isOwner(Category $category, User $user): bool
    {
        return $category->getOwner()->getId() === $user->getId();
    }
}
