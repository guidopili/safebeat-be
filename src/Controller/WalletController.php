<?php declare(strict_types=1);

namespace Safebeat\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Safebeat\Annotation;
use Safebeat\Entity\User;
use Safebeat\Entity\Wallet;
use Safebeat\Entity\WalletPendingInvitation;
use Safebeat\Event\WalletEvent;
use Safebeat\Repository\WalletRepository;
use Safebeat\Service\WalletInvitationManager;
use Safebeat\Service\WalletManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\PreconditionFailedHttpException;
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
     * @IsGranted("WALLET_VIEW", subject="wallet")
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
     * @IsGranted("WALLET_EDIT", subject="wallet")
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
     * @IsGranted("WALLET_EDIT", subject="wallet")
     */
    public function inviteToWallet(Request $request, Wallet $wallet, WalletInvitationManager $walletManager): JsonResponse
    {
        $invitedUsers = [];
        foreach ($request->request->get('users', []) as $userId) {
            $user = $this->entityManager->getRepository(User::class)->find($userId);

            if (!$user instanceof User) {
                continue;
            }

            if (true === $walletManager->inviteUser($wallet, $user)) {
                $invitedUsers[] = $user->getUsername();
            }
        }

        return JsonResponse::create(['invited' => count($invitedUsers), 'invitedUsers' => $invitedUsers]);
    }

    /**
     * @Route(path="/invite-to/{wallet}/accept", name="accept_invitation", methods={"POST"})
     */
    public function acceptInvitation(Wallet $wallet, EventDispatcherInterface $eventDispatcher, WalletInvitationManager $invitationManager)
    {
        $user = $this->getUser();
        $pendingInvitation = $invitationManager->getPendingInvitation($wallet, $user);

        if (!$pendingInvitation instanceof WalletPendingInvitation) {
            throw new PreconditionFailedHttpException("You were not invited to {$wallet}");
        }

        $this->entityManager->beginTransaction();
        try {
            $wallet->addInvitedUser($user);
            $this->entityManager->remove($pendingInvitation);
            $this->entityManager->flush();

            $eventDispatcher->dispatch(
                WalletEvent::WALLET_INVITATION_ACCEPTED,
                new WalletEvent($wallet)
            );

            $this->entityManager->commit();
        } catch (\Throwable $e) {
            $this->entityManager->rollback();
            $this->entityManager->close();

            throw $e;
        }

        return JsonResponse::create(['success' => true]);
    }

    /**
     * @Route(path="/invite-to/{wallet}/decline", name="decline_invitation", methods={"POST"})
     */
    public function declineInvitation(Wallet $wallet, EventDispatcherInterface $eventDispatcher, WalletInvitationManager $invitationManager)
    {
        $user = $this->getUser();
        $pendingInvitation = $invitationManager->getPendingInvitation($wallet, $user);

        if (!$pendingInvitation instanceof WalletPendingInvitation) {
            throw new PreconditionFailedHttpException("You were not invited to {$wallet}");
        }

        $this->entityManager->beginTransaction();
        try {
            $this->entityManager->remove($pendingInvitation);
            $this->entityManager->flush();

            $eventDispatcher->dispatch(WalletEvent::WALLET_INVITATION_DECLINED, new WalletEvent($wallet));

            $this->entityManager->commit();
        } catch (\Throwable $e) {
            $this->entityManager->rollback();
            $this->entityManager->close();

            throw $e;
        }

        return JsonResponse::create(['success' => true]);
    }

    /**
     * @Route(path="/invite-to/{wallet}", name="remove_from_wallet", methods={"DELETE"})
     * @IsGranted("WALLET_EDIT", subject="wallet")
     */
    public function removeFromWallet(Request $request, Wallet $wallet, WalletInvitationManager $invitationManager): JsonResponse
    {
        $removedUsers = [];
        foreach ($request->request->get('users', []) as $userId) {
            $user = $this->entityManager->getRepository(User::class)->find($userId);

            if (!$user instanceof User) {
                continue;
            }

            if (true === $invitationManager->removeInvitedUser($wallet, $user)) {
                $removedUsers[] = $user->getUsername();
            }
        }

        $this->entityManager->flush();

        return JsonResponse::create(['removed' => count($removedUsers), 'removedUsers' => $removedUsers]);
    }
}
