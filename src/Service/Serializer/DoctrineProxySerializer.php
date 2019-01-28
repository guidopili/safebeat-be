<?php declare(strict_types=1);

namespace Safebeat\Service\Serializer;

use Doctrine\ORM\Proxy\Proxy;
use Safebeat\Entity\BaseEntity;

class DoctrineProxySerializer implements SerializeInterface
{
    public static function supports($object): bool
    {
        return $object instanceof BaseEntity && false !== strrpos(get_class($object), '\\'.Proxy::MARKER.'\\');
    }

    public static function processValue($object)
    {
        if (! $object instanceof BaseEntity) {
            throw new \LogicException('Call support first');
        }

        return ['id' => $object->getId(), 'isProxy' => true];
    }
}
