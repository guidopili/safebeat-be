<?php declare(strict_types=1);

namespace Safebeat\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Safebeat\Annotation;
use Safebeat\Service\WalletManager;
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
     * @Annotation\RequestBodyValidator()
     */
    public function create(Request $request, WalletManager $walletManager)
    {
        $wallet = $walletManager->create($request->request->get('title'), $this->getUser());

        return JsonResponse::create(['wallet' => $wallet]);
    }
}
