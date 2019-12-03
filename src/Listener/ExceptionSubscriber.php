<?php declare(strict_types=1);

namespace Safebeat\Listener;

use Safebeat\Entity\User;
use Safebeat\Service\UserMessageTranslator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\{Event\ExceptionEvent,
    KernelEvents,
    Exception\HttpException};
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ExceptionSubscriber implements EventSubscriberInterface
{
    private const PARAM_CONVERTER_NOT_FOUND_MESSAGE = 'object not found by the @ParamConverter annotation.';
    private const ACCESS_DENIED_SECURITY_MESSAGE = 'Access Denied by controller annotation ';

    private UserMessageTranslator $translator;
    private TokenStorageInterface $tokenStorage;
    private string $env;

    public function __construct(UserMessageTranslator $translator, TokenStorageInterface $tokenStorage, string $env)
    {
        $this->translator = $translator;
        $this->tokenStorage = $tokenStorage;
        $this->env = $env;
    }

    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getException();

        if (!$exception instanceof HttpException) {

            if ($this->env === 'prod') {
                $event->setResponse(
                    JsonResponse::create(['message' => 'Internal server error'], 500)
                );
            }

            return;
        }

        $user = $this->tokenStorage->getToken() ? $this->tokenStorage->getToken()->getUser() : null;

        $params = [];
        $message = $this->tryConvertMessage($exception->getMessage(), $params);

        if ($user instanceof User) {
            $message = $this->translator->translateForUser($user, $message, $params);
        }

        $event->setResponse(
            JsonResponse::create(['message' => $message], $exception->getStatusCode())
        );
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException'
        ];
    }

    private function tryConvertMessage(string $message, array &$params): string
    {
        if (false !== strpos($message, self::PARAM_CONVERTER_NOT_FOUND_MESSAGE)) {
            return $this->convertNotFoundMessage($message, $params);
        }

        if (false !== strpos($message, self::ACCESS_DENIED_SECURITY_MESSAGE)) {
            return 'You are not authorized to work on the resource you requested';
        }

        return $message;
    }

    private function convertNotFoundMessage(string $message, array &$params): string
    {
        if (false === preg_match('/^Safebeat\\\\Entity\\\\(\w+).*/', $message, $matches)) {
            return 'Not found';
        }

        $params['%EntityName%'] = $matches[1] ?? '';
        return strtr('%EntityName% not found', $params);
    }
}
