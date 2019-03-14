<?php declare(strict_types=1);

namespace Safebeat\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWSProvider\JWSProviderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\TokenExtractor\TokenExtractorInterface;
use Safebeat\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Safebeat\Exception\RefreshTokenException;
use Safebeat\Model\RefreshTokenRequestModel;
use Safebeat\Repository\RefreshTokenRepository;
use Safebeat\Service\RefreshTokenManager;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class SecurityController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/register", name="register", methods={"POST"})
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
     * @Route("/health", name="health", methods={"GET"})
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
     * @Route("/refresh-token", name="refresh_token", methods={"POST"})
     */
    public function refreshToken(Request $request, RefreshTokenManager $refreshTokenManager): JsonResponse
    {
        $this->entityManager->beginTransaction();
        try {
            $tokenRequestModel = RefreshTokenRequestModel::buildFromRequest($request);
            $user = $this->getUser();

            $refreshTokenManager->purgeTokens($user, $tokenRequestModel);

            $refreshToken = $refreshTokenManager->create(
                $user,
                $tokenRequestModel
            );

            $this->entityManager->commit();
        } catch (\Exception $e) {
            $this->entityManager->rollback();

            throw new BadRequestHttpException($e->getMessage());
        }

        return JsonResponse::create(['refreshToken' => $refreshToken]);
    }

    /**
     * @Route("/new-token", name="new_token", methods={"POST"})
     */
    public function newToken(
        Request $request,
        JWSProviderInterface $encoder,
        JWTTokenManagerInterface $tokenManager,
        RefreshTokenRepository $tokenRepository,
        TokenExtractorInterface $tokenExtractor,
        UserProviderInterface $userProvider
    ): JsonResponse {

        $refreshTokenModel = RefreshTokenRequestModel::buildFromRequest($request);

        $token = $tokenExtractor->extract($request);

        if (!$token) {
            throw new BadRequestHttpException('Missing "X-Jwt-Auth" header');
        }

        try {
            $decodedToken = $encoder->load($token)->getPayload();
        } catch (\Throwable $e) {
            throw new BadRequestHttpException('Invalid "X-Jwt-Auth" header');
        }

        if (!is_array($decodedToken) || !array_key_exists('username', $decodedToken)) {
            throw new BadRequestHttpException('Invalid "X-Jwt-Auth" header');
        }

        $user = $userProvider->loadUserByUsername($decodedToken['username']);
        if (!$tokenRepository->isRefreshTokenValid($request->request->get('refreshToken'), $user, $refreshTokenModel)) {
            throw RefreshTokenException::invalidRefreshTokenProvided();
        }

        return JsonResponse::create(['token' => $tokenManager->create($user)]);
    }
}
