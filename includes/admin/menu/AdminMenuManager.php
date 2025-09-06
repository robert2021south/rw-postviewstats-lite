<?php
namespace RobertWP\PostViewStatsLite\Admin\Menu;

use RobertWP\PostViewStatsLite\Admin\UI\SettingsRenderer;
use RobertWP\PostViewStatsLite\Traits\Singleton;

class AdminMenuManager {
    use Singleton;

    public function add_settings_menu() {
        add_menu_page(
            __('Page view statistics', 'rw-postviewstats-lite'),          // page title
            __('RW PostViewStats Lite', 'rw-postviewstats-lite'),              // menu title
            'manage_options',          // Permission requirement
            'rwpsl-settings', // menu slug
            [SettingsRenderer::class, 'render_settings_page'], // Callback function
            null
        );

        // 注意：当子菜单中仅存在一个且与主菜单 slug 相同，WordPress 会默认隐藏该子菜单项。
        add_submenu_page(
            'rwpsl-settings',
            __('Settings', 'rw-postviewstats-lite'),
            __('Settings', 'rw-postviewstats-lite'),
            'manage_options',
            'rwpsl-settings',
            [SettingsRenderer::class, 'render_settings_page']
        );

    }
}
