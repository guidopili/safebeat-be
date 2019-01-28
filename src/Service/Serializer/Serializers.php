<?php declare(strict_types=1);

namespace Safebeat\Service\Serializer;

class Serializers
{
    /**
     * @return array | SerializeInterface[]
     */
    public static function getSerializers(): array
    {
        return [
            DateTimeSerializer::class,
            BaseEntitySerializer::class,
            DoctrineProxySerializer::class,
            NullSerializer::class,
        ];
    }
}
