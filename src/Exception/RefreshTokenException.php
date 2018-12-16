<?php declare(strict_types=1);

namespace Safebeat\Exception;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class RefreshTokenException extends BadRequestHttpException
{
    private function __construct(string $message, int $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $previous, $code);
    }

    public static function notEnoughInfoDevice(): self
    {
        return new self('Not enough device info to create a refresh token');
    }

    public static function invalidRefreshTokenProvided()
    {
        return new self('Invalid refresh token provided');
    }
}
