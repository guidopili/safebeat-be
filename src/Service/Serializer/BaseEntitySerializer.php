<?php declare(strict_types=1);

namespace Safebeat\Service\Serializer;

use Doctrine\ORM\Proxy\Proxy;
use Safebeat\Entity\BaseEntity;

class BaseEntitySerializer implements SerializeInterface
{
    public static function supports($object): bool
    {
        return $object instanceof BaseEntity && false === strrpos(get_class($object), '\\'.Proxy::MARKER.'\\');;
    }

    public static function processValue($object): array
    {
        if (! $object instanceof BaseEntity) {
            throw new \LogicException('Call support first');
        }

        return $object->jsonSerialize();
    }
}
