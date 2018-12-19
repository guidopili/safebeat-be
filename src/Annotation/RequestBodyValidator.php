<?php declare(strict_types=1);

namespace Safebeat\Annotation;

/**
 * @Annotation
 */
class RequestBodyValidator
{
    public $validator;

    public function __construct()
    {
        if (func_num_args() === 0) {
            return;
        }

        if (func_num_args() === 1) {
            $arg = func_get_arg(0);

            if (function_exists($arg)) {
                $this->validator = $arg;

                return;
            }

            if (class_exists($arg) && method_exists($arg, '__invoke')) {
                $this->validator = [$arg, '__invoke'];
            }

            return;
        }

        [$arg1, $arg2] = func_get_args();

        if (class_exists($arg1) && method_exists($arg1, $arg2)) {
            $this->validator = [$arg1, $arg2];

            return;
        }

        if (class_exists($arg2) && !method_exists($arg2, $arg1)) {
            $this->validator = [$arg2, $arg1];

            return;
        }

        if (class_exists($arg1) && method_exists($arg1,'__invoke')) {
            $this->validator = [$arg1, '__invoke'];

            return;
        }

        if (class_exists($arg2) && method_exists($arg2,'__invoke')) {
            $this->validator = [$arg2, '__invoke'];
        }
    }
}
