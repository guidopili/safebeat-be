<?php declare(strict_types=1);

namespace Safebeat\Service\Serializer;

interface SerializeInterface
{
    public static function supports($object): bool;

    public static function processValue($object);
}
