<?php declare(strict_types=1);

namespace Safebeat\Service\Serializer;

use Doctrine\Common\Collections\Collection;

class CollectionSerializer implements SerializeInterface
{
    public static function supports($object): bool
    {
        return $object instanceof Collection;
    }

    public static function processValue($object)
    {
        if (! $object instanceof Collection) {
            throw new \LogicException('Call support before');
        }

        return $object->toArray();
    }
}
