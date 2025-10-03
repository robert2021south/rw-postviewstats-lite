<?php
// tests/acceptance/_bootstrap.php

use Codeception\Exception\ModuleConfigException;
use Codeception\Exception\ModuleException;
use Codeception\Lib\ModuleContainer;
use lucatume\WPBrowser\Module\WPWebDriver;

/** @var ModuleContainer $container */
global $container; // Codeception 会自动注入

/** @var WPWebDriver $wpModule */
try {
    $wpModule = $container->getModule('WPWebDriver');
} catch (ModuleException $e) {

}

// 设置 admin 用户和密码
if (getenv('WP_ADMIN_USER') && getenv('WP_ADMIN_PASS')) {
    try {
        $wpModule->_setConfig([
            'adminUsername' => getenv('WP_ADMIN_USER'),
            'adminPassword' => getenv('WP_ADMIN_PASS')
        ]);
    } catch (ModuleConfigException|ModuleException $e) {

    }
}
