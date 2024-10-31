<?php

spl_autoload_register(
    function($class)
    {
        if (file_exists(__DIR__ . '/' . $class . '.php')) {
            $classPath = str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
            require_once $classPath;
        }
    }
);