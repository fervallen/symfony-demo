<?php

namespace App\EventListener;

use App\Kernel;
use App\Response\ExceptionErrorResponse;
use Helpcrunch\Helper\SentryHelper;
use Exception;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

class ApiExceptionListener
{
    const DEFAULT_EXCEPTION_ERROR_MESSAGE = 'Server error';

    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        /** @var Kernel $kernel */
        $kernel = $this->container->get('kernel');
        if ($kernel->isConsoleApplication() || !$kernel::isApi($event->getRequest())) {
            return;
        }

        $exception = $event->getException();
        if ($kernel->isProd()) {
            SentryHelper::logException($exception);
            $event->setResponse(new ExceptionErrorResponse(
                self::DEFAULT_EXCEPTION_ERROR_MESSAGE,
                $exception
            ));
        } else {
            $event->setResponse(new ExceptionErrorResponse(
                $exception->getMessage(),
                $exception,
                $kernel->isProd() ? [] : $this->getExceptionDetails($exception)
            ));
        }
    }

    private function getExceptionDetails(Exception $exception): array
    {
        $details = [$exception->getFile() . ': ' . $exception->getLine()];
        $details = array_merge($details, $exception->getTrace());

        return $details;
    }
}
