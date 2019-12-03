<?php declare(strict_types=1);

namespace Safebeat\Service\Serializer;

class NullSerializer implements SerializeInterface
{
    public static function supports($object): bool
    {
        return $object === null;
    }

    public static function processValue($object): string
    {
        if (null !== $object) {
            throw new \LogicException('Call support before');
        }

        return '';
    }
}
