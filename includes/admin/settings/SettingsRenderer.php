<?php
namespace RobertWP\PostViewStatsLite\Admin\Settings;

use RobertWP\PostViewStatsLite\Utils\TemplateLoader;

if (!defined('ABSPATH')) exit;

class SettingsRenderer {

    public static function render_checkbox_setting_field(array $field, string $page_slug, string $section_id)
    {
        add_settings_field(
            $field['id'],
            $field['label'],
            function () use ($field) {
                $option = $field['option'];

                // 网络设置页或站点未启用全局设置
                $all_settings = get_option(SettingsRegistrar::OPTION_SITE_SETTINGS, []);
                $value = isset($all_settings[$option]) ? $all_settings[$option] : '0';

                $checked = checked($value, '1', false);

                TemplateLoader::load('partials/checkbox-field', [
                    'option' => $option,
                    'checked' => $checked,
                    'desc' => $field['desc'],
                ]);
            },
            $page_slug,
            $section_id
        );

    }

}