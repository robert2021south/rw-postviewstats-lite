<?php
// ============================
// WordPress 测试环境配置
// ============================

// 数据库配置（优先用环境变量）
define( 'DB_NAME',     getenv('WP_DB_NAME') ?: 'test_db' );
define( 'DB_USER',     getenv('WP_DB_USER') ?: 'root' );
define( 'DB_PASSWORD', getenv('WP_DB_PASSWORD') ?: 'root' );
define( 'DB_HOST',     getenv('WP_DB_HOST') ?: '127.0.0.1' );

const DB_CHARSET = 'utf8';
const DB_COLLATE = '';

// WordPress 路径
define( 'WP_TESTS_DIR', getenv('WP_DIR') ?: dirname(__FILE__) . '/../wp/' );
define( 'WP_PLUGIN_DIR', dirname(__FILE__) . '/../' );

// 开启 WP_DEBUG
const WP_DEBUG = true;

// 加载 WordPress 测试环境
require_once WP_TESTS_DIR . '/wp-load.php';

// 如果你有 bootstrap 文件，加载它
$bootstrap = WP_PLUGIN_DIR . '/tests/_bootstrap.php';
if ( file_exists( $bootstrap ) ) {
    require_once $bootstrap;
}
