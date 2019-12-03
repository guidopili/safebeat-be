<?php declare(strict_types=1);

namespace Safebeat\Voter;

use Safebeat\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

abstract class AbstractVoter extends Voter
{
    protected Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function commonChecks(TokenInterface $token): array
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return [null, false];
        }

        if ($this->security->isGranted('ROLE_SUPER_ADMIN')) {
            return [$user, true];
        }

        return [$user, null];
    }
}
