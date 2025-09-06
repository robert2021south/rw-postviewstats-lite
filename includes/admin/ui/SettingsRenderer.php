<?php
namespace RobertWP\PostViewStatsLite\Admin\UI;

use RobertWP\PostViewStatsLite\Utils\Helper;
use RobertWP\PostViewStatsLite\Utils\TemplateLoader;

class SettingsRenderer {

    public static function render_settings_page() {
        $upgrade_url = Helper::get_upgrade_url('setting-page');
        TemplateLoader::load('settings/settings-page',[
            'upgrade_url'=>$upgrade_url
        ]);
    }

}