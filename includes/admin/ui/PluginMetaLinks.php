<?php
namespace RobertWP\PostViewStatsLite\Admin\UI;

class PluginMetaLinks {
    public static function add_links($links){
        $settings_link = '<a href="' . admin_url('admin.php?page=rwpsl-settings') . '">' . __('Settings', 'rw-postviewstats-lite') . '</a>';
        array_unshift($links, $settings_link);
        return $links;

    }
}