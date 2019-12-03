<?php declare(strict_types=1);

namespace Safebeat\Entity;

use Doctrine\ORM\Mapping as ORM;
use Safebeat\Service\Serializer\Serializers;

abstract class BaseEntity implements \JsonSerializable
{
    /**
     * @var int
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    public function getId()
    {
        return $this->id;
    }

    public function jsonSerialize(): array
    {
        $ref = new \ReflectionObject($this);

        $ret = [];
        foreach ($ref->getProperties() as $property) {
            $property->setAccessible(true);

            $propertyValue = $property->getValue($this);

            foreach (Serializers::getSerializers() as $serializerClass) {
                if ($serializerClass::supports($propertyValue)) {
                    $propertyValue = $serializerClass::processValue($propertyValue);
                }
            }

            if ($propertyValue !== null && empty($propertyValue)) {
                continue; // Avoid empty strings when object is fetched from db - Stupid Doctrine
            }

            $ret[$property->getName()] = $propertyValue;
        }

        return $ret;
    }
}
