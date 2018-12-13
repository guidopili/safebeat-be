<?php declare(strict_types=1);

namespace Safebeat\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="refresh_token")
 * @ORM\Entity()
 */
final class RefreshToken
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User")
     */
    private $user;
    /**
     * @var string
     * @ORM\Column(name="refresh_token", type="string", length=100)
     */
    private $refreshToken;

    public function __construct(User $user, string $refreshToken)
    {
        $this->user = $user;
        $this->refreshToken = $refreshToken;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getRefreshToken(): string
    {
        return $this->refreshToken;
    }
}
