<?php declare(strict_types=1);

namespace Safebeat\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Table(name="user")
 * @ORM\Entity()
 */
class User extends BaseEntity implements UserInterface
{
    /**
     * @var string
     * @ORM\Column(name="password", nullable=false, type="string", length=100)
     */
    private $password;
    /**
     * @var string
     * @ORM\Column(name="username", nullable=false, type="string", length=50, unique=true)
     */
    private $username;
    /**
     * @var string
     * @ORM\Column(name="roles", nullable=false, type="simple_array")
     */
    private $roles;
    /**
     * @var string
     * @ORM\Column(name="email", nullable=true, type="string")
     */
    private $email;

    public function __construct(string $username, string $password)
    {
        $this->username = $username;
        $this->password = $password;
        $this->roles = ['role_admin'];
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getSalt()
    {
    }

    public function eraseCredentials()
    {
    }

    public function jsonSerialize(): array
    {
        $ret = parent::jsonSerialize();

        unset($ret['password'], $ret['roles']);

        if (empty($ret['email'])) {
            unset($ret['email']);
        }

        return $ret;
    }
}
