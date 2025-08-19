<?php


if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

use RobertWP\PostViewStatsLite\Admin\Settings\SettingsRegistrar;
use RobertWP\PostViewStatsLite\Core\Bootstrap;

Bootstrap::uninstall();

rwpsl_delete_all_options_metas();

function rwpsl_delete_all_options_metas(){
    $option_names = array(
        OPTION_RWPSL_VERSION,
        SettingsRegistrar::OPTION_SITE_SETTINGS,
    );

    foreach ($option_names as $option_name) {
        delete_option(sanitize_key($option_name));
    }

    //可能删除统计数据
    $all_settings = get_option(SettingsRegistrar::OPTION_SITE_SETTINGS, []);
    $value = isset($all_settings['delete_data_on_uninstall']) ? $all_settings['delete_data_on_uninstall'] : '0';
    if($value){
        global $wpdb;
        $query = $wpdb->prepare(
            "DELETE FROM $wpdb->postmeta WHERE meta_key like %s",
            '_rwpsl_%'
        );
        $wpdb->query($query);
    }

}
