<?php declare(strict_types=1);

namespace Safebeat\Listener;

use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\EntityManagerInterface;
use Safebeat\Entity\TimeStampable;

class TimeStampableListener
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function prePersist(LifecycleEventArgs $eventArgs)
    {
        $entity = $eventArgs->getObject();

        if (!$this->isTimestampable($entity)) {
            return;
        }

        $entity->setCreatedAt(new \DateTime());
        $this->entityManager->flush();
    }

    public function preUpdate(LifecycleEventArgs $eventArgs)
    {
        $entity = $eventArgs->getObject();

        if (!$this->isTimestampable($entity)) {
            return;
        }

        $entity->setUpdatedAt(new \DateTime());
        $this->entityManager->flush();
    }

    private function isTimestampable($entity): bool
    {
        try {
            $class = new \ReflectionClass($entity);
            $trait = array_search(TimeStampable::class, $class->getTraitNames());
        } catch (\Throwable $e) {
            return false;
        }

        return false !== $trait;
    }
}
