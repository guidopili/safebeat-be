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
    /**
     * @var string | null
     * @ORM\Column(name="first_name", nullable=true, type="string", length=50)
     */
    private $firstName;
    /**
     * @var string | null
     * @ORM\Column(name="last_name", nullable=true, type="string", length=50)
     */
    private $lastName;
    /**
     * @var string | null
     * @ORM\Column(name="language", nullable=true, type="language_enum")
     */
    private $language;

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
