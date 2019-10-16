<?php

namespace App\EventListener;

use App\Controller\Site\ExceptionController;
use App\Kernel;
use App\Service\KnowledgeBaseInitializerService;
use Helpcrunch\Helper\SentryHelper;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SiteExceptionListener
{
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
        if ($kernel->isConsoleApplication() || $kernel::isApi($event->getRequest())) {
            return;
        }

        $exceptionController = new ExceptionController($this->container);
        $exception = $event->getException();

        if ($exception instanceof NotFoundHttpException) {
            $event->setResponse($exceptionController->actionNotFound($exception));
            return;
        }

        if ($kernel->isStage()) {
            $event->setResponse($exceptionController->actionError($exception->getMessage()));
        } elseif ($kernel->isProd()) {
            SentryHelper::logException($exception);
            $event->setResponse($exceptionController->actionError());
        }
    }
}
