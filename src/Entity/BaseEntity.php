<?php declare(strict_types=1);

namespace Safebeat\Entity;

use Doctrine\ORM\Mapping as ORM;
use Safebeat\Service\Serializer\Serializers;

abstract class BaseEntity implements \JsonSerializable
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

    public function jsonSerialize(): array
    {
        $ref = new \ReflectionObject($this);
        $properties = $ref->getProperties();

        $ret = [];
        foreach ($properties as $property) {
            $property->setAccessible(true);

            $propertyValue = $property->getValue($this);

            foreach (Serializers::getSerializers() as $serializerClass) {
                if ($serializerClass::supports($propertyValue)) {
                    $propertyValue = $serializerClass::processValue($propertyValue);
                }
            }

            $ret[$property->getName()] = $propertyValue;
        }

        return $ret;
    }
}
