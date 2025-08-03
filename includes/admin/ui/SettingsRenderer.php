<?php
namespace RobertWP\PostViewStatsLite\Admin\UI;

if (!defined('ABSPATH')) exit;

use RobertWP\PostViewStatsLite\Utils\TemplateLoader;

class SettingsRenderer {

    public static function render_settings_page() {
        TemplateLoader::load('settings/settings-page');
    }

}