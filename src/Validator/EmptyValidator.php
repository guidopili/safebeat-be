<?php declare(strict_types=1);

namespace Safebeat\Validator;

class EmptyValidator implements ValidatorInterface
{
    public function validate($value): bool
    {
        return !empty($value);
    }
}
