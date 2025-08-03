<?php
/*
 *
 * */
namespace RobertWP\PostViewStatsLite\Modules\Sort;

if (!defined('ABSPATH')) exit;

use RobertWP\PostViewStatsLite\Admin\Settings\SettingsRegistrar;
use RobertWP\PostViewStatsLite\Modules\Tracker\Tracker;
use RobertWP\PostViewStatsLite\Traits\Singleton;


class Sort {
    use Singleton;

    public static function maybe_register_hooks() {

        if (SettingsRegistrar::get_effective_setting('sort_enabled') !== 1) {
            return;
        }

        $post_types = get_post_types(['public' => true], 'names'); // 获取所有公开的文章类型
        foreach ($post_types as $type) {
            add_filter("manage_edit-{$type}_sortable_columns", [self::class, 'make_views_column_sortable'], 20);
        }

        add_filter('pre_get_posts', [self::class, 'add_view_count_sorting'], 20);
    }

    public static function add_view_count_sorting($query) {
        if (!is_admin() && $query->is_main_query() && $query->get('orderby') === 'views') {
            $query->set('meta_key', Tracker::RWPSL_META_KEY_TOTAL);
            $query->set('orderby', 'meta_value_num');
        }
    }

    public static function make_views_column_sortable($columns) {
        $columns['post_views'] = 'views';
        return $columns;
    }

}
