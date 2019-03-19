<?php declare(strict_types=1);

namespace Safebeat\Repository;

use Doctrine\ORM\EntityRepository;

class NotificationRepository extends EntityRepository
{
    public const DEFAULT_ALIAS = 'notification';

    public function getNotifications(bool $read): array
    {
        $queryBuilder = $this->createQueryBuilder(self::DEFAULT_ALIAS);

        if (false === $read) {
            $queryBuilder->andWhere('notification.read = :read');
            $queryBuilder->setParameter('read', $read);
        }

        return $queryBuilder->getQuery()->getResult();
    }
}
