<?php declare(strict_types=1);

namespace Safebeat\Validator;

class NumericValidator implements ValidatorInterface
{
    public function validate($value): bool
    {
        return is_numeric($value);
    }
}
