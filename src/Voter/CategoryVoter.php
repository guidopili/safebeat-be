<?php declare(strict_types=1);

namespace Safebeat\Voter;

use Safebeat\Entity\Category;
use Safebeat\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class CategoryVoter extends Voter
{
    private const ATTRIBUTE_VIEW = 'CATEGORY_VIEW';
    private const ATTRIBUTE_DELETE = 'CATEGORY_DELETE';

    private const SUPPORTED_ATTRIBUTES = [
        self::ATTRIBUTE_VIEW,
        self::ATTRIBUTE_DELETE,
    ];

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports($attribute, $subject): bool
    {
        return $subject instanceof Category && in_array($attribute, self::SUPPORTED_ATTRIBUTES, true);
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
