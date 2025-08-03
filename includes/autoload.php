<?php
if (!defined('ABSPATH')) exit;

spl_autoload_register(function ($class) {
    $prefix = 'RobertWP\\PostViewStatsLite\\';

    if (strpos($class, $prefix) !== 0) {
        return;
    }

    $relative_class = substr($class, strlen($prefix));

    $parts = explode('\\', $relative_class);

    $class_name = array_pop($parts);
    $sub_path = count($parts) ? implode('/', array_map('strtolower', $parts)) . '/' : '';

    $file = __DIR__ . '/' . $sub_path . $class_name . '.php';

    if (file_exists($file)) {
        require $file;
    } else {
        error_log("Autoload error: File not found for class $class => $file");
    }
});


