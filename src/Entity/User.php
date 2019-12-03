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
     * @ORM\Column(name="password", nullable=false, type="string", length=100)
     */
    private string $password;
    /**
     * @ORM\Column(name="username", nullable=false, type="string", length=50, unique=true)
     */
    private string $username;
    /**
     * @ORM\Column(name="roles", nullable=false, type="simple_array")
     */
    private array $roles;
    /**
     * @ORM\Column(name="email", nullable=true, type="string")
     */
    private ?string $email = null;
    /**
     * @ORM\Column(name="first_name", nullable=true, type="string", length=50)
     */
    private ?string $firstName = null;
    /**
     * @ORM\Column(name="last_name", nullable=true, type="string", length=50)
     */
    private ?string $lastName = null;
    /**
     * @ORM\Column(name="language", nullable=true, type="language_enum")
     */
    private ?string $language = null;

    public function __construct(string $username, string $password)
    {
        $this->username = $username;
        $this->password = $password;
        $this->roles = ['role_user'];
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

    public function getFullName(): string
    {
        if (empty($this->firstName) || empty($this->lastName)) {
            return $this->username;
        }

        return "$this->firstName $this->lastName";
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    public function getLanguage(): ?string
    {
        return $this->language;
    }

    public function setLanguage(string $language): void
    {
        $this->language = $language;
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

    public function __toString(): string
    {
        return $this->getFullName();
    }
}
