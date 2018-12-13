<?php declare(strict_types=1);

namespace Safebeat\Service;

use Safebeat\Entity\RefreshToken;
use Safebeat\Entity\User;
use Safebeat\Model\RefreshTokenRequestModel;

class RefreshTokenManager
{
    public function create(User $user, RefreshTokenRequestModel $tokenRequestModel): RefreshToken
    {

    }
}
