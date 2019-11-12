<?php declare(strict_types=1);

namespace Safebeat\Validator;

class NullStringValidator implements ValidatorInterface
{
    public function validate($value): bool
    {
        return 0 !== strcasecmp($value, 'null');
    }
}
