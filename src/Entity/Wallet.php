<?php declare(strict_types=1);

namespace Safebeat\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Safebeat\Repository\WalletRepository")
 * @ORM\Table("wallet")
 */
class Wallet extends BaseEntity implements \JsonSerializable
{
    use TimeStampable;

    /**
     * @var string
     * @ORM\Column(name="title", type="string", length=50, nullable=false, unique=true)
     */
    private $title;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $owner;

    /**
     * @var bool
     * @ORM\Column(name="deleted", type="boolean", nullable=false, options={"default"=0})
     */
    private $deleted = false;

    public function getOwner(): User
    {
        return $this->owner;
    }

    public function setOwner(User $owner): void
    {
        $this->owner = $owner;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function isDeleted(): bool
    {
        return $this->deleted ?? false;
    }

    public function setDeleted(bool $deleted): void
    {
        $this->deleted = $deleted;
    }
}
