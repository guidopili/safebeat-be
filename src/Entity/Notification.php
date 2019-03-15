<?php declare(strict_types=1);

namespace Safebeat\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table("notification")
 */
class Notification extends BaseEntity
{
    /**
     * @var boolean
     * @ORM\Column(name="is_read", type="boolean", nullable=false)
     */
    private $read;

    /**
     * @var string
     * @ORM\Column(name="content", type="string", nullable=false)
     */
    private $content;

    /**
     * @var string
     * @ORM\Column(name="title", type="string", nullable=false)
     */
    private $title;

    /**
     * @var array
     * @ORM\Column(name="links", type="json", nullable=false)
     */
    private $links;

    public function __construct()
    {
        $this->read = false;
        $this->links = [];
    }

    public function isRead(): bool
    {
        return $this->read;
    }

    public function setRead(bool $read): void
    {
        $this->read = $read;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getLinks(): array
    {
        return $this->links;
    }

    public function setLinks(array $links): void
    {
        $this->links = $links;
    }

    public function addLink(string $link, string $category): void
    {
        $this->links[$category] = $link;
    }
}
