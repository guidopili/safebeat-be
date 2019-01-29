<?php declare(strict_types=1);

namespace Safebeat\Service;

use Doctrine\ORM\EntityManagerInterface;
use Safebeat\Entity\RefreshToken;
use Safebeat\Entity\User;
use Safebeat\Model\RefreshTokenRequestModel;

class RefreshTokenManager
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function create(User $user, RefreshTokenRequestModel $tokenRequestModel): string
    {
        $token = bin2hex(random_bytes(40));
        $refreshToken = new RefreshToken($user, $token . $tokenRequestModel->__toString());

        $this->entityManager->persist($refreshToken);
        $this->entityManager->flush();

        return $token;
    }

    public function purgeTokens(User $user, RefreshTokenRequestModel $tokenRequestModel): void
    {
        $statement = $this->entityManager->getConnection()->prepare(
            "DELETE FROM refresh_token WHERE user_id = ? AND refresh_token LIKE ?"
        );

        $statement->execute([$user->getId(), '%'.$tokenRequestModel->__toString()]);
    }
}
