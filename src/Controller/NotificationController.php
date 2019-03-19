<?php declare(strict_types=1);

namespace Safebeat\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Safebeat\Entity\Notification;
use Safebeat\Repository\NotificationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/notification", name="notification_")
 */
class NotificationController extends AbstractController
{
    /**
     * @Route("/", name="list", methods={"GET"})
     */
    public function getAction(Request $request, NotificationRepository $notificationRepository): JsonResponse
    {
        return JsonResponse::create(
            [
                'notifications' => $notificationRepository
                    ->getNotifications(
                        filter_var($request->request->get('all', false), FILTER_VALIDATE_BOOLEAN)
                    ),
            ]
        );
    }

    /**
     * @Route("/{notification}", name="mark_read", methods={"PUT"})
     */
    public function markAsRead(Notification $notification, EntityManagerInterface $entityManager): JsonResponse
    {
        if ($notification->isRead()) {
            return JsonResponse::create(
                [
                    'message' => 'Notification already marked as read',
                ],
                JsonResponse::HTTP_ALREADY_REPORTED
            );
        }

        $notification->setRead(true);
        $entityManager->flush();

        return JsonResponse::create(null, JsonResponse::HTTP_NO_CONTENT);
    }
}
