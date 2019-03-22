<?php declare(strict_types=1);

namespace Safebeat\Entity;

use Doctrine\Common\Collections\{ArrayCollection,Collection};
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Safebeat\Repository\WalletRepository")
 * @ORM\Table("wallet")
 */
class Wallet extends BaseEntity
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
    private $deleted;

    /**
     * @var Collection
     * @ORM\ManyToMany(targetEntity="User", fetch="EXTRA_LAZY")
     * @ORM\JoinTable(name="wallet_invited_user",
     *   joinColumns={
     *     @ORM\JoinColumn(name="wallet_id", referencedColumnName="id", onDelete="CASCADE")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     *   }
     * )
     */
    private $invitedUsers;

    public function __construct()
    {
        $this->invitedUsers = new ArrayCollection();
        $this->deleted = false;
    }

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

    public function getInvitedUsers(): Collection
    {
        return $this->invitedUsers;
    }

    public function setInvitedUsers(Collection $invitedUsers): void
    {
        $this->invitedUsers = $invitedUsers;
    }

    public function addInvitedUser(User $user): void
    {
        $this->invitedUsers->add($user);
    }

    public function removeInvitedUser(User $user): bool
    {
        return $this->invitedUsers->removeElement($user);
    }

    public function hasInvitedUser(User $user): bool
    {
        return $this->invitedUsers->contains($user);
    }

    public function __toString(): string
    {
        return $this->getTitle();
    }
}
