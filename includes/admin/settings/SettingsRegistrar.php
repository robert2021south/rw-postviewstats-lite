<?php
namespace RobertWP\PostViewStatsLite\Admin\Settings;

use RobertWP\PostViewStatsLite\Traits\Singleton;
use RobertWP\PostViewStatsLite\Utils\TemplateLoader;

if (!defined('ABSPATH')) exit;

class SettingsRegistrar {
    use Singleton;

    const OPTION_SITE_SETTINGS = 'rwpsl_site_settings';

    public function register_settings() {
        self::register_settings_fields('rwpsl-settings');
    }

    public static function register_settings_fields($page_slug){

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
                'desc'  => __('When enabled, the page views of each article will be automatically counted.', 'rw-postviewstats-lite')
            ],
            [
                'id' => 'rwpsl_sort_field',
                'option' => 'sort_enabled',
                'label' => __('Enable sorting', 'rw-postviewstats-lite'),
                'desc'  => __('When enabled, You can sort the articles on the article list page by clicking "Views".', 'rw-postviewstats-lite')
            ],
            [
                'id' => 'rwpsl_rest_api_field',
                'option' => 'rest_api_enabled',
                'label' => __('Enable REST API', 'rw-postviewstats-lite'),
                'desc'  => __('When enabled, you can retrieve the view count of a specific post via the REST API.', 'rw-postviewstats-lite')
            ],
        ];

        foreach ($fields as $field) {
            add_settings_field(
                $field['id'],
                $field['label'],
                function () use ($field) {
                    $option = $field['option'];

                    // 网络设置页或站点未启用全局设置
                    $all_settings = get_option(self::OPTION_SITE_SETTINGS, []);
                    $value = isset($all_settings[$option]) ? $all_settings[$option] : '0';

                    $checked = checked($value, '1', false);

                    TemplateLoader::load('partials/checkbox-field', [
                        'option' => $option,
                        'checked' => $checked,
                        'desc' => $field['desc'],
                    ]);
                },
                $page_slug,
                'rwpsl_feature_section'
            );
        }
    }

    // 获取有效配置
    public static function get_effective_setting($key) {
        $site_settings = get_option(self::OPTION_SITE_SETTINGS, []);
        return isset($site_settings[$key]) ? $site_settings[$key] : '0';
    }

}