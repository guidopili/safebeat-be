<?php declare(strict_types=1);

namespace Safebeat\Service\Notification;

use Doctrine\ORM\EntityManagerInterface;
use Safebeat\Entity\Notification;
use Safebeat\Entity\User;
use Safebeat\Service\Notification\Notifiers\NotifierInterface;
use Safebeat\Service\Notification\Notifiers\RealTimeNotifier;

class NotificationService
{
    /**
     * @var NotifierInterface[]
     */
    private array $notifiers;
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager, RealTimeNotifier $notifier)
    {
        $this->entityManager = $entityManager;
        $this->notifiers = [$notifier];
    }

    public function sendNotificationToUser(User $user, Notification $notification): void
    {
        $this->entityManager->persist($notification);
        $this->entityManager->flush();

        foreach ($this->notifiers as $notifier) {
            if (!$notifier::isSupportedBy($user)) {
                continue;
            }

            $notifier->notify($user, $notification);
        }
    }
}
