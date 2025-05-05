<?php

/**
 * Simple PSR-4 Autoloader for GPL Times plugin
 */

spl_autoload_register(function ($class) {
    // Base namespace for the plugin
    $namespace = 'Inc\\';

    // Check if the class uses our namespace
    if (strpos($class, $namespace) !== 0) {
        return;
    }

    // Get the relative class name
    $relative_class = substr($class, strlen($namespace));

    // Replace namespace separator with directory separator
    $file = str_replace('\\', '/', $relative_class) . '.php';

    // Get the full path to the file
    $path = dirname(__FILE__) . '/inc/' . $file;

    // If the file exists, require it
    if (file_exists($path)) {
        require_once $path;
    }
});
