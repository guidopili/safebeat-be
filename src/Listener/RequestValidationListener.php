<?php declare(strict_types=1);

namespace Safebeat\Listener;

use Psr\Log\LoggerInterface;
use Safebeat\Annotation\RequestBodyValidator;
use Safebeat\Service\EnhancedReflection;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

class RequestValidationListener
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function log(FilterControllerEvent $event)
    {
        if (!is_array($event->getController())) {
            return;
        }

        [$controller, $methodName] = $event->getController();

        $this->logger->info(get_class($controller));
        $this->logger->info($methodName);

        $r = new EnhancedReflection($controller);

        $searched = RequestBodyValidator::class;
        $useStatements = $r->hasUseStatement($searched);

        if (false === $useStatements) {
            return;
        }

        $docs = $r->getMethod($methodName)->getDocComment();

        $a = explode('\\', $useStatements);
        $el = array_pop($a).'\RequestBodyValidator';
        $this->logger->info($el);

        $this->logger->info(sprintf("Replace %s with %s", $el, RequestBodyValidator::class));
        $this->logger->info($r = str_replace("@$el", '@'.RequestBodyValidator::class, $docs));

        $exploded = explode(PHP_EOL, $r);

        $parsed = [];
        foreach ($exploded as $item) {
            if (1 === preg_match('/^\s*\*\s*\@/', $item)) {
                $parsed[] = $item;

                continue;
            }
        }

        $exploded = array_filter($exploded, function(string $e) {return false !== strpos($e, RequestBodyValidator::class);});
        $exploded = array_pop($exploded);
        $exploded = preg_replace('/^\s*\*\s*\@/', '', $exploded);

        $this->logger->info(print_r($exploded, true));
    }
}
