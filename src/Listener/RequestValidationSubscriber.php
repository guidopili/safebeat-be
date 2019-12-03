<?php declare(strict_types=1);

namespace Safebeat\Listener;

use Doctrine\Common\Annotations\AnnotationReader;
use Psr\Log\LoggerInterface;
use Safebeat\Annotation\RequestBodyValidator;
use Safebeat\Validator\ValidatorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class RequestValidationSubscriber implements EventSubscriberInterface
{
    private LoggerInterface $logger;
    private AnnotationReader $annotationReader;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->annotationReader = new AnnotationReader();
    }

    public function validateRequestBody(ControllerEvent $event)
    {
        if (!is_array($event->getController())) {
            return;
        }

        $request = $event->getRequest();
        if ('GET' === $request->getMethod()) {
            return;
        }

        $annotation = $this
            ->annotationReader
            ->getMethodAnnotation(
                new \ReflectionMethod(...$event->getController()),
                RequestBodyValidator::class
            );

        if (!$annotation instanceof RequestBodyValidator || !is_array($annotation->validators)) {
            return;
        }

        $body = json_decode($request->getContent(), true, JSON_THROW_ON_ERROR);
        foreach ($annotation->validators as $key => $callable) {
            $this->validateSingleValue($callable, $body, $key);
        }
    }

    private function validateSingleValue($callable, array $body, string $key): void
    {
        if (!isset($body[$key])) {
            throw new BadRequestHttpException("Missing '$key' in body");
        }

        if (is_callable($callable)) {
            if (false === call_user_func($callable, $body[$key])) {
                throw new BadRequestHttpException("Value '{$body[$key]}' for key '$key' is not valid");
            }
            return;
        }

        if (is_array($callable)) {
            foreach ($callable as $item) {
                $this->validateSingleValue($item, $body, $key);
            }

            return;
        }

        if (!class_exists($callable)) {
            return;
        }

        if (
            (is_subclass_of($callable, ValidatorInterface::class) && false === (new $callable)->validate($body[$key]))
            || (method_exists($callable, '__invoke') && false === $callable->__invoke($body[$key]))
        ) {
            throw new BadRequestHttpException("Value '{$body[$key]}' for key '$key' is not valid");
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'validateRequestBody'
        ];
    }
}
