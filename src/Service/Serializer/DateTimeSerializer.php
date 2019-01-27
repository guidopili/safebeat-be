<?php declare(strict_types=1);

namespace Safebeat\Service\Serializer;

class DateTimeSerializer implements SerializeInterface
{
    public static function supports($object): bool
    {
        return $object instanceof \DateTime;
    }

    public static function processValue($object)
    {
        if (! $object instanceof \DateTime ) {
            throw new \LogicException('Call support before');
        }

        return $object->format(\DateTimeInterface::ISO8601);
    }
}
