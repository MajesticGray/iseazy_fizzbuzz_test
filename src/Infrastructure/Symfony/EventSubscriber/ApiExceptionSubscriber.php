<?php

namespace App\Infrastructure\Symfony\EventSubscriber;

use ApiPlatform\Validator\Exception\ValidationException;
use App\Domain\Exception\FizzBuzzException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class ApiExceptionSubscriber implements EventSubscriberInterface
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        // Handle only non controlled exceptions
        if ($exception instanceof FizzBuzzException || $exception instanceof ValidationException) {
            return;
        }

        $exception  = $event->getThrowable();
        $statusCode = $exception instanceof HttpExceptionInterface ? $exception->getStatusCode() : 500;
        $message    = $exception instanceof HttpExceptionInterface ? $exception->getMessage() : 'Internal Server Error';

        $response = new JsonResponse(
            data: ['errors' => [
                [
                    'title'  => $message,
                    'status' => $statusCode,
                    'code'   => $statusCode,
                ],
            ]],
        );

        $event->setResponse($response);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => ['onKernelException', 20],
        ];
    }
}
