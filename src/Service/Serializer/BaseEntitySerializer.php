<?php declare(strict_types=1);

namespace Safebeat\Service\Serializer;

use Safebeat\Entity\BaseEntity;

class BaseEntitySerializer implements SerializeInterface
{
    public static function supports($object): bool
    {
        return $object instanceof BaseEntity;
    }

    public static function processValue($object)
    {
        if (! $object instanceof BaseEntity) {
            throw new \LogicException('Call support first');
        }

        return $object->jsonSerialize();
    }
}
