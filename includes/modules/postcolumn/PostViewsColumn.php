<?php
/*
 *
 * */
namespace RobertWP\PostViewStatsLite\Modules\PostColumn;

use RobertWP\PostViewStatsLite\Modules\Tracker\Tracker;

if (!defined('ABSPATH')) exit;


class PostViewsColumn {

    // Add the "Views" column to the article list
    public function add_views_column($columns) {
        $columns['post_views'] =  __('Views', 'rw-postviewstats-lite');
        return $columns;
    }

    // Display page view data
    public function display_views_column($column, $post_id) {
        if ('post_views' === $column) {
            $views = get_post_meta($post_id, Tracker::RWPSL_META_KEY_TOTAL, true);
            echo $views ? esc_html($views) : '0';
        }
    }

}
