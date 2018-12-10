<?php declare(strict_types=1);

namespace Safebeat\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Safebeat\Entity\User;
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
     */
    public function register(Request $request, EncoderFactoryInterface $encoder)
    {
        $username = $request->request->get('username');
        $password = $request->request->get('password');
        $user = new User($username, $password);
        $user->setPassword($encoder->getEncoder($user)->encodePassword($password, $user->getSalt()));
        $user->setEmail('asd');
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        return new JsonResponse([sprintf('User %s successfully created', $user->getUsername())]);
    }
}
