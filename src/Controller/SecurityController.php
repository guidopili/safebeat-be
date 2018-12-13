<?php declare(strict_types=1);

namespace Safebeat\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Safebeat\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

class SecurityController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/api/register", name="register")
     * @Method("POST")
     */
    public function register(Request $request, EncoderFactoryInterface $encoder): JsonResponse
    {
        $user = new User(
            $request->request->get('username'),
            $encoder->getEncoder(User::class)->encodePassword($request->request->get('password'), null)
        );

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return new JsonResponse(["message" => "User {$user->getUsername()} successfully created"]);
    }

    /**
     * @Route("/api/health", name="health")
     * @Method("GET")
     */
    public function health(): JsonResponse
    {
        try {
            $this->entityManager->getConnection()->prepare('SELECT 1')->execute();
        } catch (\Throwable $e) {
            return JsonResponse::create(['success' => false, 'errorMessage' => $e->getMessage()]);
        }

        return JsonResponse::create(['success' => true]);
    }

    /**
     * @Route("/api/refresh-token", name="refresh-token")
     * @Method("POST")
     */
    public function refreshToken()
    {
    }
}
