<?php declare(strict_types=1);

namespace Safebeat\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Fig\Link\Link;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Psr\Log\LoggerInterface;
use Safebeat\Entity\Notification;
use Safebeat\Repository\NotificationRepository;
use Safebeat\Service\Notification\TopicBuilder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/notification", name="notification_")
 */
class NotificationController extends AbstractController
{
    private $mercureHub;
    private $logger;

    public function __construct(string $mercureHub, LoggerInterface $logger)
    {
        $this->mercureHub = $mercureHub;
        $this->logger = $logger;
    }

    /**
     * @Route(name="list", methods={"GET"})
     */
    public function getAction(Request $request, NotificationRepository $notificationRepository): JsonResponse
    {
        $this->addLink($request, new Link('mercure', 'http://safebeat.io/hub'));

        $token = (new Builder())
            // set other appropriate JWT claims, such as an expiration date
            ->set('mercure', ['subscribe' => TopicBuilder::buildTopicForUser($this->getUser())])
            ->sign(new Sha256(), 'PCdv6N6xPjPmmm6eVbt6sWYpZJp4Wju9')
            ->getToken();

        return JsonResponse::create(
            [
                'notifications' => $notificationRepository
                    ->getNotifications(
                        filter_var($request->request->get('all', false), FILTER_VALIDATE_BOOLEAN)
                    ),
            ],
            JsonResponse::HTTP_OK,
            [
                'set-cookie' => sprintf('mercureAuthorization=%s; path=/hub; secure; httponly; SameSite=strict', $token)
            ]
        );
    }

    /**
     * @Route("/{notification}", name="mark_read", methods={"PUT"})
     * @IsGranted("NOTIFICATION_EDIT", subject="notification")
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
