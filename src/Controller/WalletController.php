<?php declare(strict_types=1);

namespace Safebeat\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Safebeat\Entity\Wallet;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/wallet", name="wallet_")
 */
class WalletController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route(name="create", methods={"POST"})
     */
    public function create(Request $request)
    {
        $wallet = new Wallet();

        $wallet->setTitle($request->request->get('title'));

        $this->entityManager->persist($wallet);
        $this->entityManager->flush();

        return JsonResponse::create(['wallet' => $wallet]);
    }
}
