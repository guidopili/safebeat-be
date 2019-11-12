<?php declare(strict_types=1);

namespace Safebeat\Validator;

class PasswordValidator implements ValidatorInterface
{
    public function validate($value): bool
    {
        if (!is_string($value)) {
            return false;
        }

        if (mb_strlen($value) < 8) {
            return false;
        }

        if (!preg_match('/[a-zA-Z]/', $value)) {
            return false;
        }

        if (!preg_match('/\d/', $value)) {
            return false;
        }

        if (!preg_match('/[^a-zA-Z\d]/', $value)) {
            return false;
        }

        return true;
    }
}
