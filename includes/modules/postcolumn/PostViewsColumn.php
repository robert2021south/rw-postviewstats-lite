<?php
/*
 *
 * */
namespace RobertWP\PostViewStatsLite\Modules\PostColumn;

use RobertWP\PostViewStatsLite\Modules\Tracker\Tracker;

if (!defined('ABSPATH')) exit;


class PostViewsColumn {

    // Add the "Views" column to the article list
    public function maybe_add_views_column($columns) {
        if ($this->is_pro_plugin_active()) {
            return $columns; // Pro 插件已启用，跳过添加列
        }

        $columns['post_views'] =  __('Views', 'rw-postviewstats-lite');
        return $columns;
    }

    // Display page view data
    public function maybe_display_views_column($column, $post_id) {
        if ($this->is_pro_plugin_active()) {
            return; // Pro 插件已启用，跳过添加列
        }
        if ('post_views' === $column) {
            $views = get_post_meta($post_id, Tracker::RWPSL_META_KEY_TOTAL, true);
            echo $views ? esc_html($views) : '0';
        }
    }

    // 检测 Pro 插件是否启用
    private function is_pro_plugin_active() {
        if (!function_exists('is_plugin_active')) {
            include_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        return is_plugin_active('rw-postviewstats-pro/rw-postviewstats-pro.php');
    }

}
