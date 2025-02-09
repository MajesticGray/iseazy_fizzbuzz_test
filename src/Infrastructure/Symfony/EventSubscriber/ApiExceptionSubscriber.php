<?php

namespace App\Infrastructure\Symfony\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * This class listens uncontrolled exceptions and reformats
 *   the response as JSON for API consistency
 */
class ApiExceptionSubscriber implements EventSubscriberInterface
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        // Handle only non controlled exceptions
        if (!$exception instanceof HttpException) {
            return;
        }

        $statusCode = $exception instanceof HttpExceptionInterface ? $exception->getStatusCode() : 500;
        $message    = $exception->getMessage();

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
