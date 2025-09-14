<?php
namespace RobertWP\PostViewStatsLite\Admin\Settings;

use RobertWP\PostViewStatsLite\Traits\Singleton;

class SettingsRegistrar {
    use Singleton;

    const OPTION_SITE_SETTINGS = 'rwpsl_site_settings';

    public function register_settings(): void
    {
        self::register_settings_fields('rwpsl-settings');
    }

    public static function register_settings_fields($page_slug): void
    {
        // === 第一组设置：功能设置 ===
        add_settings_section(
            'rwpsl_feature_section',
            __('Feature Settings', 'rw-postviewstats-lite'),
            null,
            $page_slug
        );

        $fields = [
            [
                'id' => 'rwpsl_stat_field',
                'option' => 'stat_enabled',
                'label' => __('Enable page view statistics', 'rw-postviewstats-lite'),
                'desc' => __('When enabled, the page views of each article will be automatically counted.', 'rw-postviewstats-lite')
            ],
            [
                'id' => 'rwpsl_sort_field',
                'option' => 'sort_enabled',
                'label' => __('Enable sorting', 'rw-postviewstats-lite'),
                'desc' => __('When enabled, You can sort the articles on the article list page by clicking "Views".', 'rw-postviewstats-lite')
            ],
            [
                'id' => 'rwpsl_rest_api_field',
                'option' => 'rest_api_enabled',
                'label' => __('Enable REST API', 'rw-postviewstats-lite'),
                'desc' => __('When enabled, you can retrieve the view count of a specific post via the REST API.', 'rw-postviewstats-lite')
            ],
        ];

        foreach ($fields as $field) {
            SettingsRenderer::render_checkbox_setting_field($field, $page_slug, 'rwpsl_feature_section' );
        }

        // === 第二组设置：数据设置 ===
        add_settings_section(
            'rwpsp_data_section',
            __('Data Settings', 'rw-postviewstats-lite'),
            null,
            $page_slug
        );

        $data_fields = [
            [
                'id' => 'rwpsp_delete_data_field',
                'option' => 'delete_data_on_uninstall',
                'label' => __('Delete data on uninstall', 'rw-postviewstats-lite'),
                'desc'  => __('When checked, all statistical data will be permanently deleted when the plugin is uninstalled.', 'rw-postviewstats-lite')
            ]
        ];

        foreach ($data_fields as $field) {
            SettingsRenderer::render_checkbox_setting_field($field, $page_slug, 'rwpsp_data_section');
        }
    }

    // 获取有效配置
    public static function get_effective_setting($key) {
        $site_settings = get_option(self::OPTION_SITE_SETTINGS, []);
        return $site_settings[$key] ?? '0';
    }

}