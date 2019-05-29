<?php declare(strict_types=1);

namespace Safebeat\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Safebeat\Entity\Category;
use Safebeat\Entity\MoneyTransaction;
use Safebeat\Entity\Wallet;
use Safebeat\Repository\MoneyTransactionRepository;
use Safebeat\Service\MoneyTransactionManager;
use Safebeat\Service\UserMessageTranslator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Safebeat\Annotation\RequestBodyValidator;
use Safebeat\Validator\{EmptyValidator, NumericValidator};

/**
 * @Route("/transaction", name="transaction_")
 */
class MoneyTransactionController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route(name="list", methods={"GET"})
     */
    public function listTransactions(MoneyTransactionRepository $transactionRepository): JsonResponse
    {
        return JsonResponse::create(
            ['transactions' => $transactionRepository->getTransactionsByOwner($this->getUser())]
        );
    }

    /**
     * @Route(path="/{wallet}/list", name="list_wallet", methods={"GET"})
     * @IsGranted("WALLET_VIEW", subject="wallet")
     */
    public function listByWallet(Wallet $wallet, MoneyTransactionRepository $transactionRepository): JsonResponse
    {
        return JsonResponse::create(['transactions' => $transactionRepository->getTransactionsListByWallet($wallet)]);
    }

    /**
     * @Route(path="/{transaction}", name="get", methods={"GET"})
     * @IsGranted("TRANSACTION_VIEW", subject="transaction")
     */
    public function getTransaction(MoneyTransaction $transaction): JsonResponse
    {
        return JsonResponse::create(['transaction' => $transaction]);
    }

    /**
     * @Route(name="create", methods={"POST"})
     * @RequestBodyValidator(validators={
     *     "description": EmptyValidator::class,
     *     "amount": {EmptyValidator::class, NumericValidator::class}
     * })
     */
    public function create(Request $request, MoneyTransactionManager $transactionManager): JsonResponse
    {
        $amount = $request->request->get('amount');
        $description = $request->request->get('description');
        $categoryId = $request->request->get('category');
        $walletId = $request->request->get('wallet');

        if (is_numeric($categoryId)) {
            $category = $this->entityManager->find(Category::class, $categoryId);
        }

        if (is_numeric($walletId)) {
            $wallet = $this->entityManager->find(Wallet::class, $walletId);
        }

        // Add check to see if user is authorized to add transaction

        $transaction = $transactionManager->create(
            $this->getUser(),
            (float)$amount,
            $description,
            $category ?? null,
            $wallet ?? null
        );

        return JsonResponse::create(['transaction' => $transaction], 201);
    }

    /**
     * @Route(path="/{transaction}", name="delete", methods={"DELETE"})
     * @IsGranted("TRANSACTION_DELETE", subject="transaction")
     */
    public function delete(
        MoneyTransaction $transaction,
        MoneyTransactionManager $transactionManager,
        UserMessageTranslator $translator
    ): JsonResponse {
        if ($transaction->getOwner() !== $this->getUser()) {
            throw new AccessDeniedHttpException(
                $translator->translateForUser($this->getUser(), "This transaction doesn't belong to you!")
            );
        }

        return JsonResponse::create(['deleted' => $transactionManager->delete($transaction)]);
    }

    /**
     * @Route(path="/{transaction}", name="update", methods={"PUT"})
     * @IsGranted("TRANSACTION_EDIT", subject="transaction")
     */
    public function update(
        Request $request,
        MoneyTransaction $transaction,
        MoneyTransactionManager $transactionManager
    ): JsonResponse {

        $updatedTransaction = $transactionManager->update($transaction, $request->request->all());

        return JsonResponse::create(['transaction' => $updatedTransaction]);
    }
}
