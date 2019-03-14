<?php declare(strict_types=1);

namespace Safebeat\Controller\CRUD;

use Doctrine\ORM\EntityManagerInterface;
use Safebeat\Annotation;
use Safebeat\Entity\User;
use Safebeat\Entity\Wallet;
use Safebeat\Repository\WalletRepository;
use Safebeat\Service\WalletManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
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
     * @Route(name="list", methods={"GET"})
     */
    public function listWallets(WalletRepository $walletRepository): JsonResponse
    {
        return JsonResponse::create(['wallets' => $walletRepository->getWalletListByUser($this->getUser())]);
    }

    /**
     * @Route(path="/{wallet}", name="get", methods={"GET"})
     */
    public function getWallet(Wallet $wallet): JsonResponse
    {
        return JsonResponse::create(['wallet' => $wallet]);
    }

    /**
     * @Route(name="create", methods={"POST"})
     * @Annotation\RequestBodyValidator()
     */
    public function create(Request $request, WalletManager $walletManager): JsonResponse
    {
        $title = $request->request->get('title');

        if (empty($title)) {
            throw new BadRequestHttpException('Missing required title in body');
        }

        $wallet = $walletManager->create($title, $this->getUser());

        return JsonResponse::create(['wallet' => $wallet], 201);
    }

    /**
     * @Route(path="/{wallet}", name="delete", methods={"DELETE"})
     */
    public function delete(Wallet $wallet, WalletManager $walletManager): JsonResponse
    {
        if ($wallet->getOwner() !== $this->getUser()) {
            throw new AccessDeniedHttpException('This wallet doesn\'t belong to you!');
        }

        return JsonResponse::create(['deleted' => $walletManager->delete($wallet)]);
    }

    /**
     * @Route(path="/{wallet}", name="update", methods={"PUT"})
     */
    public function update(Request $request, Wallet $wallet, WalletManager $walletManager): JsonResponse
    {
        if ($wallet->getOwner() !== $this->getUser()) {
            throw new AccessDeniedHttpException('This wallet doesn\'t belong to you!');
        }

        $updatedWallet = $walletManager->update($wallet, $request->request->all());

        return JsonResponse::create(['wallet' => $updatedWallet]);
    }

    /**
     * @Route(path="/invite-to/{wallet}", name="invite_to_wallet", methods={"POST"})
     */
    public function inviteToWallet(Request $request, Wallet $wallet, WalletManager $walletManager): JsonResponse
    {
        if ($wallet->getOwner() !== $this->getUser()) {
            throw new AccessDeniedHttpException('This wallet doesn\'t belong to you!');
        }

        $invitedUsers = [];
        foreach ($request->request->get('users', []) as $userId) {
            $user = $this->entityManager->getRepository(User::class)->find($userId);

            if (!$user instanceof User) {
                continue;
            }

            if (true === $walletManager->inviteUsers($wallet, $user)) {
                $invitedUsers[] = $user->getUsername();
            }
        }

        return JsonResponse::create(['invited' => count($invitedUsers), 'invitedUsers' => $invitedUsers]);
    }

    /**
     * @Route(path="/invite-to/{wallet}", name="remove_from_wallet", methods={"DELETE"})
     */
    public function removeFromWallet(Request $request, Wallet $wallet, WalletManager $walletManager): JsonResponse
    {
        if ($wallet->getOwner() !== $this->getUser()) {
            throw new AccessDeniedHttpException('This wallet doesn\'t belong to you!');
        }

        $userIds = $request->request->get('users', []);

        $removedUsers = [];
        foreach ($userIds as $userId) {
            $user = $this->entityManager->getRepository(User::class)->find($userId);

            if (!$user instanceof User) {
                continue;
            }

            // TODO move this to manager
            if ($wallet->removeInvitedUser($user)) {
                $removedUsers[] = $user->getUsername();
            }
        }

        $this->entityManager->flush();

        return JsonResponse::create(['removed' => count($removedUsers), 'removedUsers' => $removedUsers]);
    }
}
