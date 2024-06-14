<?php

use App\AppKernel;
use App\Kernel;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\Dotenv\Exception\PathException;
use Symfony\Component\ErrorHandler\Debug;

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

return function (array $context) {
    try {
        (new Dotenv(true))->load(__DIR__ . '/../.env');
    } catch (PathException $exception) {
        // ignore, application is running in k8s
    }

    if ($context['APP_ENV'] === 'dev') {
        Debug::enable();
    }
    return new AppKernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
    //return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
