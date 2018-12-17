<?php declare(strict_types=1);

namespace Safebeat\Entity;

use Doctrine\ORM\Mapping as ORM;

trait TimeStampable
{
    /**
     * @var \Datetime|null
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    protected $updatedAt;

    /**
     * @var \Datetime | null
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    protected $createdAt;

    public function getUpdatedAt(): ?\Datetime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\Datetime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function getCreatedAt(): ?\Datetime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\Datetime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }
}
