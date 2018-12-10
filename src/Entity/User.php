<?php declare(strict_types=1);

namespace Safebeat\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Table(name="user")
 * @ORM\Entity()
 */
class User implements UserInterface
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    /**
     * @var string
     * @ORM\Column(name="password", nullable=false, type="string", length=100)
     */
    private $password;
    /**
     * @var string
     * @ORM\Column(name="salt", nullable=false, type="string", length=20)
     */
    private $salt;
    /**
     * @var string
     * @ORM\Column(name="username", nullable=false, type="string", length=50)
     */
    private $username;
    /**
     * @var string
     * @ORM\Column(name="roles", nullable=false, type="simple_array")
     */
    private $roles;
    /**
     * @var string
     * @ORM\Column(name="email", nullable=false, type="string")
     */
    private $email;

    public function __construct(string $username, string $password)
    {
        $this->username = $username;
        $this->password = $password;
        $this->roles = ['role_admin'];
        $this->salt = uniqid();
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setPassword(string $password)
    {
        $this->password = $password;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getSalt(): string
    {
        return $this->salt;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function eraseCredentials()
    {
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id): void
    {
        $this->id = $id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }
}
