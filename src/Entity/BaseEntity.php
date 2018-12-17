<?php declare(strict_types=1);

namespace Safebeat\Entity;

use Doctrine\ORM\Mapping as ORM;

abstract class BaseEntity
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    public function getId()
    {
        return $this->id;
    }
}
