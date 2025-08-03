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
        if (is_multisite() && self::is_network_wide_activation()) {
            // 网络激活：为主站和所有子站激活
            $sites = get_sites(['fields' => 'ids']);
            foreach ($sites as $site_id) {
                switch_to_blog($site_id);
                self::activate_single_site();
                restore_current_blog();
            }

            // 切换到主站创建主站专用表和全局设置
            switch_to_blog(get_main_site_id());
            //self::activate_main_site_shared_resources();
            restore_current_blog();
        } else {
            // 单站点，或子站点单独激活
            self::activate_single_site();

            // 如果当前站点不是主站，则额外确保主站资源存在
            if (is_multisite() && !is_main_site()) {
                switch_to_blog(get_main_site_id());
                //self::activate_main_site_shared_resources();
                restore_current_blog();
            }
        }
    }

    public static function deactivate(): void {
        self::rwpsl_delete_all_options_metas();
    }

    public static function uninstall(): void {

    }

    private static function activate_single_site(): void {
        self::create_shared_site_settings();
    }

    private static function create_shared_site_settings(): void {
        update_option(OPTION_RWPSL_VERSION, RWPSL_PLUGIN_VERSION);
    }

    private static function is_network_wide_activation(): bool {
        // 更安全地判断是否为网络激活：适用于 WP CLI 和后台激活
        return isset($_GET['networkwide']) || (defined('WP_CLI') && WP_CLI && !is_main_site());
    }

    private static function rwpsl_delete_all_options_metas()
    {
        $option_names = array(
            OPTION_RWPSL_VERSION,
            SettingsRegistrar::OPTION_SITE_SETTINGS,
        );

        foreach ($option_names as $option_name) {
            delete_option(sanitize_key($option_name));
        }

        //
        global $wpdb;
        $query = $wpdb->prepare(
            "DELETE FROM $wpdb->postmeta WHERE meta_key like %s",
            '_rwpsl_%'
        );
        $wpdb->query($query);

    }


}