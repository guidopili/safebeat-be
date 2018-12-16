<?php declare(strict_types=1);

namespace Safebeat\Repository;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Security\Core\User\UserInterface;
use Safebeat\Model\RefreshTokenRequestModel;

class RefreshTokenRepository extends EntityRepository
{
    public function isRefreshTokenValid(string $refreshToken, UserInterface $user, RefreshTokenRequestModel $requestModel): bool
    {
        return 0 < $this->count(['user' => $user, 'refreshToken' => $refreshToken.$requestModel->__toString()]);
    }
}
