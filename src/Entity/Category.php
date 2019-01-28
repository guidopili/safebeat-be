<?php declare(strict_types=1);

namespace Safebeat\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="category")
 */
class Category extends BaseEntity
{
    use TimeStampable;

    /**
     * @var string
     * @ORM\Column(name="name", type="string", length=25, nullable=false, unique=true)
     */
    private $name;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $owner;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getOwner(): User
    {
        return $this->owner;
    }

    public function setOwner(User $owner): void
    {
        $this->owner = $owner;
    }
}
