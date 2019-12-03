<?php declare(strict_types=1);

namespace Safebeat\Service;

use Psr\Log\LoggerInterface;
use Safebeat\Entity\User;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserMessageTranslator
{
    private TranslatorInterface $translator;
    private LoggerInterface $logger;

    public function __construct(TranslatorInterface $translator, LoggerInterface $logger)
    {
        $this->translator = $translator;
        $this->logger = $logger;
    }

    public function translateForUser(User $user, string $message, array $parameters = []): string
    {
        return $this->translator->trans(
            $message,
            $parameters,
            null,
            $user->getLanguage()
        );
    }
}
