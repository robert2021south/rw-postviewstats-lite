<?php
namespace RobertWP\PostViewStatsLite\Core;

if (!defined('ABSPATH')) exit;

use RobertWP\PostViewStatsLite\Admin\Settings\SettingsRegistrar;
use RobertWP\PostViewStatsLite\Utils\TemplateLoader;


class Bootstrap {
    private static bool $initialized = false;

    public static function run(): void {
        if (self::$initialized) {
            return;
        }

        // 2. 注册所有钩子
        HooksRegistrar::register();

        // 4. 初始化基础组件 初始化模板加载器
        TemplateLoader::init(plugin_dir_path(RWPSL_PLUGIN_FILE));

        // 5. 按版本(Lite、Pro、Lifetime)加载功能
        Loader::load_features();

        self::$initialized = true;
    }

    public static function activate(): void {
        update_option(OPTION_RWPSL_VERSION, RWPSL_PLUGIN_VERSION);

        $data = [];
        $data['stat_enabled'] = 1;
        $data['sort_enabled'] = 1;
        $data['rest_api_enabled'] = 1;
        $data['delete_data_on_uninstall'] = 0;
        update_option( SettingsRegistrar::OPTION_SITE_SETTINGS, $data );
    }

    public static function deactivate(): void {

    }

    public static function uninstall(): void {

    }


}