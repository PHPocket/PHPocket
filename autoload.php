<?php
/**
 * Base autoloader file
 * Just include this file
 */

namespace PHPocket;

/**
 * Autoload function, than handles only framework's namespace
 *
 * @param string $className
 * @return void
 */
function autoloaderFunctionForPHPocketFramework($className)
{
    if (substr($className, 0, strlen(__NAMESPACE__)) !== __NAMESPACE__) {
        return;
    }
    if (strpos($className, '..') !== false) {
        return;
    }

    include __DIR__ . '/src/' . substr(str_replace('\\', '/', $className), strlen(__NAMESPACE__) + 1) . '.php';
}

spl_autoload_register(__NAMESPACE__ . '\\autoloaderFunctionForPHPocketFramework');