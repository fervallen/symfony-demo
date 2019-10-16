<?php

namespace App;

use Helpcrunch\Response\ErrorResponse;
use Helpcrunch\Response\InnerErrorCodes;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\RouteCollectionBuilder;

class Kernel extends BaseKernel {
    use MicroKernelTrait;

    const APPLICATION_CACHE_DIRECTORY = '/var/cache/';
    const APPLICATION_LOG_PATH = '/var/log';
    const CONFIG_EXTS = '.{php,xml,yaml,yml}';

    public function getLogDir(): string
    {
        return $this->getProjectDir() . self::APPLICATION_LOG_PATH;
    }

    public function registerBundles()
    {
        $contents = require __DIR__ . '/../config/bundles.php';
        foreach ($contents as $class => $envs) {
            if (isset($envs['all']) || isset($envs[$this->environment])) {
                yield new $class();
            }
        }
    }

    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader): void
    {
        $container->addResource(new FileResource($this->getProjectDir() . '/config/bundles.php'));
        $container->setParameter('container.autowiring.strict_mode', true);
        $container->setParameter('container.dumper.inline_class_loader', true);
        $container->setParameter('database', DatabaseHelper::selectDatabase());
        $confDir = $this->getProjectDir() . '/config';

        $loader->load($confDir . '/{packages}/*' . self::CONFIG_EXTS, 'glob');
        $loader->load($confDir . '/{packages}/' . $this->environment . '/**/*' . self::CONFIG_EXTS, 'glob');
        $loader->load($confDir . '/{services}' . self::CONFIG_EXTS, 'glob');
        $loader->load($confDir . '/{services}_' . $this->environment . self::CONFIG_EXTS, 'glob');
    }

    protected function configureRoutes(RouteCollectionBuilder $routes): void
    {
        $confDir = $this->getProjectDir() . '/config';
        $routes->import($confDir . '/{routes}/*' . self::CONFIG_EXTS, '/', 'glob');
        $routes->import($confDir . '/{routes}/' . $this->environment . '/**/*' . self::CONFIG_EXTS, '/', 'glob');
        $routes->import($confDir . '/{routes}' . self::CONFIG_EXTS, '/', 'glob');
    }

    public function handle(Request $request, $type = HttpKernelInterface::MASTER_REQUEST, $catch = true): Response
    {
        if (!$this->isConsoleApplication()) {
            try {
                $request = $this->populateJsonRequest($request);
            } catch (BadRequestHttpException $exception) {
                return new ErrorResponse($exception->getMessage(), InnerErrorCodes::MALFORMED_JSON);
            }
        }

        return parent::handle($request, $type, $catch);
    }

    // This one is needed to process both json and classic request parameters
    public function populateJsonRequest(Request $request): Request
    {
        if (empty($request->request->all()) && !empty($request->getContent())) {
            $parameters = json_decode($request->getContent());
            if (is_null($parameters) && $request->getContent() && ($request->getContent() != 'NULL')) {
                throw new BadRequestHttpException('Malformed json');
            }
            foreach ($parameters as $name => $value) {
                $request->request->set($name, $value);
            }
        }

        return $request;
    }

    public function isConsoleApplication(): bool
    {
        return (php_sapi_name() == 'cli') ||
            (defined('STDIN')) ||
            (array_key_exists('SHELL', $_ENV)) ||
            (!array_key_exists('REQUEST_METHOD', $_SERVER));
    }
}
