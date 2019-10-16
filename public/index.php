<?php

use App\Kernel;
use Helpcrunch\Helper\SentryHelper;
use Symfony\Component\Debug\Debug;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpFoundation\Request;

const ENVIRONMENT_PROD = 'prod';
const ENVIRONMENT_STAGE = 'stage';
const ENVIRONMENT_DEV = 'dev';

require __DIR__.'/../vendor/autoload.php';
if (!isset($_SERVER['APP_ENV'])) {
    (new Dotenv())->load(__DIR__.'/../.env');
}
if ($_SERVER['APP_ENV'] == ENVIRONMENT_PROD) {
    SentryHelper::install();
}

$env = $_SERVER['APP_ENV'] ?? ENVIRONMENT_DEV;
$debug = (bool) ($_SERVER['APP_DEBUG'] ?? ($env != ENVIRONMENT_PROD));

if ($debug) {
    umask(0000);
    Debug::enable();
}

$kernel = new Kernel($env, $debug);
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
