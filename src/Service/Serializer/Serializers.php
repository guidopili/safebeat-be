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
            CollectionSerializer::class,
            DateTimeSerializer::class,
            BaseEntitySerializer::class,
            DoctrineProxySerializer::class,
            NullSerializer::class,
        ];
    }
}
