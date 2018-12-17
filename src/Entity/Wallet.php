<?php declare(strict_types=1);

namespace Safebeat\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table("wallet")
 */
class Wallet extends BaseEntity implements \JsonSerializable
{
    use TimeStampable;

    /**
     * @var string
     * @ORM\Column(name="title", type="string", length=50, nullable=false)
     */
    private $title;

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'createdAt' => $this->getCreatedAt(),
            'updatedAt' => $this->getCreatedAt(),
        ];
    }
}
