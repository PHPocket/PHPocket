<?php

namespace PHPocket\Tests;

// Registering own tests autoloader
function autoloaderFunctionForPHPocketTests($className)
{
    if (substr($className, 0, strlen(__NAMESPACE__)) !== __NAMESPACE__) {
        return;
    }
    if (strpos($className, '..') !== false) {
        return;
    }

    include __DIR__ . '/unit/' . substr(str_replace('\\', '/', $className), strlen(__NAMESPACE__) + 1) . '.php';
}

spl_autoload_register(__NAMESPACE__ . '\\autoloaderFunctionForPHPocketTests');

// Loading PHPocket autoloader
require __DIR__ . '/../autoload.php';
