<?php

$constPath = __DIR__ . '/const.php';
if (file_exists($constPath)) {
    require_once($constPath);
}

spl_autoload_register(static function ($className) {
    $baseName = 'Agapov\Main';
    $className = trim(substr($className, strlen($baseName)), '\\');
    $classPath = __DIR__ . '/lib/' . str_replace('\\', DIRECTORY_SEPARATOR, $className) . '.php';

    if (file_exists($classPath)) {
        require_once($classPath);
    }
});
