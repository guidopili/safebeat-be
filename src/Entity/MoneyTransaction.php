<?php declare(strict_types=1);

namespace Safebeat\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Safebeat\Repository\MoneyTransactionRepository")
 * @ORM\Table(name="money_transaction")
 */
class MoneyTransaction extends BaseEntity
{
    use TimeStampable;

    /**
     * @ORM\Column(name="amount", type="float", nullable=false)
     */
    private float $amount;
    /**
     * @ORM\Column(name="description", type="string", length=80, nullable=false)
     */
    private string $description;
    /**
     * @ORM\ManyToOne(targetEntity="Category")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private ?Category $category = null;
    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private User $owner;
    /**
     * @ORM\ManyToOne(targetEntity="Wallet")
     * @ORM\JoinColumn(onDelete="SET NULL", nullable=true)
     */
    private ?Wallet $wallet = null;

    public function __construct()
    {
        $this->amount = 0;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): void
    {
        $this->amount = $amount;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): void
    {
        $this->category = $category;
    }

    public function getOwner(): User
    {
        return $this->owner;
    }

    public function setOwner(User $owner): void
    {
        $this->owner = $owner;
    }

    public function getWallet(): ?Wallet
    {
        return $this->wallet;
    }

    public function setWallet(?Wallet $wallet): void
    {
        $this->wallet = $wallet;
    }
}
