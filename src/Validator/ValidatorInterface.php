<?php declare(strict_types=1);

namespace Safebeat\Validator;

interface ValidatorInterface
{
    public function validate($value): bool;
}
