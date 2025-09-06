<?php
namespace RobertWP\PostViewStatsLite\Core;

use RobertWP\PostViewStatsLite\Admin\Menu\AdminMenuManager;
use RobertWP\PostViewStatsLite\Admin\Settings\SettingsHandler;
use RobertWP\PostViewStatsLite\Admin\Settings\SettingsRegistrar;
use RobertWP\PostViewStatsLite\Admin\UI\AdminNotice;
use RobertWP\PostViewStatsLite\Admin\UI\PluginMetaLinks;
use RobertWP\PostViewStatsLite\Assets\AdminAssets;
use RobertWP\PostViewStatsLite\Assets\FrontendAssets;
use RobertWP\PostViewStatsLite\I18n\Localization;
use RobertWP\PostViewStatsLite\Modules\Cleaner\Cleaner;
use RobertWP\PostViewStatsLite\Modules\Export\PostViewsExporter;
use RobertWP\PostViewStatsLite\Modules\PostColumn\PostViewsColumn;
use RobertWP\PostViewStatsLite\Modules\RestApi\RestApi;
use RobertWP\PostViewStatsLite\Modules\Shortcode\ShortcodeHandler;
use RobertWP\PostViewStatsLite\Modules\Sort\Sort;
use RobertWP\PostViewStatsLite\Modules\Tracker\Tracker;


class HooksRegistrar {

    public static function register() {
        self::register_textdomain_hooks();  // 放第一，语言加载早于其他逻辑更安全
        self::register_core_hooks();    // 核心功能，如版本检查、激活等
        self::register_admin_hooks();    // 管理后台钩子
        self::register_frontend_hooks();    // 前台钩子
        self::register_feature_hooks();    // 功能性模块（视具体项目结构）
    }

    private static function register_textdomain_hooks() {
        add_action('init', [Localization::class, 'load_textdomain']);
    }

    private static function register_core_hooks() {
        add_action('admin_init', self::cb([VersionChecker::class, 'check']));
        add_action('admin_init', self::cb([AdminNotice::class,'maybe_add_notice']));
    }

    private static function register_admin_hooks() {
        if (!is_admin()) return;

        $menu_manager = AdminMenuManager::get_instance();
        $settings_registrar = SettingsRegistrar::get_instance();
        $settings_handler = new SettingsHandler();
        $columns = new PostViewsColumn();

        // admin_menu
        add_action('admin_menu', [$menu_manager, 'add_settings_menu']);
        add_action('admin_menu', [PostViewsExporter::class, 'add_export_submenu']);
        add_action('admin_menu', [Cleaner::class, 'add_cleaner_submenu']);

        // admin_posthandle_network_settings_form
        add_action('admin_post_rwpsl_save_settings', self::cb([$settings_handler,'handle_settings_form']));
        add_action('admin_post_rwpsl_cleaner', self::cb([Cleaner::class, 'handle_cleaner_request']));
        add_action('admin_post_rwpsl_export_csv', self::cb([PostViewsExporter::class, 'handle_export_csv']));

        // admin_init
        add_action('admin_init', [$settings_registrar, 'register_settings']);
        add_action('admin_init', self::cb([AdminNotice::class, 'maybe_show_general_notice']));

        // option update hook
        add_action('update_option_rwpsl_settings', self::cb([$settings_handler, 'after_settings_saved']), 10, 2);

        // UI columns
        add_filter('manage_posts_columns', self::cb([$columns, 'maybe_add_views_column']));
        add_action('manage_posts_custom_column', self::cb([$columns, 'maybe_display_views_column']), 10, 2);
        add_filter('manage_page_posts_columns', self::cb([$columns, 'maybe_add_views_column']));
        add_action('manage_page_posts_custom_column', self::cb([$columns, 'maybe_display_views_column']), 10, 2);

        // plugin meta
        add_action('plugin_action_links_' . plugin_basename(RWPSL_PLUGIN_FILE), [PluginMetaLinks::class, 'add_links']);
        add_action('admin_enqueue_scripts', [AdminAssets::class, 'enqueue']);

    }

    private static function register_frontend_hooks() {
        $display = new ShortcodeHandler();
        add_shortcode('rwpsl_post_views', self::cb([$display, 'display_post_views']));

        $tracker = new Tracker();
        add_action('wp_ajax_nopriv_rwpsl_add_view', self::cb([$tracker, 'track_views_ajax']));
        add_action('wp_ajax_rwpsl_add_view', self::cb([$tracker, 'track_views_ajax']));
        add_action('wp_enqueue_scripts', [FrontendAssets::class, 'enqueue']);
    }

    private static function register_feature_hooks() {
        RestApi::maybe_register_hooks();
        Sort::maybe_register_hooks();
    }

    private static function cb($callback) {
        return CallbackWrapper::plugin_context_only($callback);
    }

}
