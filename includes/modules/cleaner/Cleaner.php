<?php
/*
 *
 * */
namespace RobertWP\PostViewStatsLite\Modules\Cleaner;

use RobertWP\PostViewStatsLite\Modules\tracker\Tracker;
use RobertWP\PostViewStatsLite\Traits\Singleton;
use RobertWP\PostViewStatsLite\Utils\Helper;
use RobertWP\PostViewStatsLite\Utils\TemplateLoader;

class Cleaner {
    use Singleton;

    public static function add_cleaner_submenu(): void
    {
        add_submenu_page(
            'rwpsl-settings',
            __('Data Cleaner','rw-postviewstats-lite'),
            __('Data Cleaner','rw-postviewstats-lite'),
            'manage_options',
            'rwpsl-cleaner',
            [self::class, 'render_cleaner_page']
        );
    }

    public static function render_cleaner_page(): void
    {

        // 准备模板需要的变量
        $nonce = sanitize_text_field( wp_unslash( $_POST['nonce'] ?? '' ) );
        $template_args = [
            'show_success_notice' => isset($_GET['cleaned'], $_GET['nonce']) &&
                $_GET['cleaned'] == '1' &&
                wp_verify_nonce($nonce, 'rwpsl_cleaned_notice'),
            'admin_post_url' => admin_url('admin-post.php'),
            'nonce_action' => 'rwpsl_cleaner_action',
            'post_types' => get_post_types(['public' => true], 'objects'),
            'default_limit' => gmdate('Ymd', strtotime('-30 days')),
            'upgrade_url'=> Helper::get_upgrade_url('cleaner')
        ];

        // 加载模板
        TemplateLoader::load('cleaner-page', $template_args, 'cleaner');
    }

    /**
     * @throws \Exception
     */
    public static function handle_cleaner_request(): void
    {
        if (!current_user_can('manage_options')) {
            wp_redirect(admin_url('admin.php?page=rwpsl-cleaner&notice=ins_perm'));
            Helper::terminate();
        }

        // Nonce 验证
        if (!isset($_POST['rwpsl_cleaner_nonce']) ||
            !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['rwpsl_cleaner_nonce'])), 'rwpsl_cleaner_action')// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        ) {
            wp_redirect(admin_url('admin.php?page=rwpsl-cleaner&notice=inv_req'));
            Helper::terminate();
        }

        $post_type  = isset($_POST['post_type']) ? sanitize_text_field(wp_unslash($_POST['post_type'])) : 'post';

        // 允许 post 和 page
        $allowed_types = ['post', 'page'];

        if (!in_array($post_type, $allowed_types, true)) {
            $post_type = 'post'; // 默认回退为 post
        }

        // 强制限制为30天前
        $date_limit = gmdate('Ymd', strtotime('-30 days'));

        // 获取目标文章ID
        $posts = get_posts(array(
            'numberposts' => -1,
            'post_type'   => $post_type,
            'post_status' => 'any',
            'fields'      => 'ids',
        ));

        foreach ($posts as $post_id) {
            $retained_today_values = [];

            // 只获取当前文章的 meta
            $post_metas = get_post_meta($post_id);

            // 先处理 _rwpsl_today_YYYYMMDD 的数据
            foreach ($post_metas as $meta_key => $values) {
                if (preg_match('/^' . Tracker::RWPSL_META_KEY_TODAY_PREFIX . '(\d{8})$/', $meta_key, $matches)) {
                    $date = $matches[1];

                    if ((int)$date >= (int)$date_limit) {
                        // // 未过期，记录数值
                        foreach ($values as $value) {
                            $retained_today_values[] = (int)$value;
                        }
                    } else {
                        // 已过期，删除
                        delete_post_meta($post_id, $meta_key);
                    }
                }
            }

            // 然后处理 _rwpsl_total
            if (metadata_exists('post', $post_id, Tracker::RWPSL_META_KEY_TOTAL)) {
                if (!empty($retained_today_values)) {
                    // 还有有效数据，更新 _rwpsl_total
                    $total = array_sum($retained_today_values);
                    update_post_meta($post_id, Tracker::RWPSL_META_KEY_TOTAL, $total);
                } else {
                    // 没有任何有效数据，删除 _rwpsl_total
                    delete_post_meta($post_id, Tracker::RWPSL_META_KEY_TOTAL);
                }
            }
        }

        //wp_redirect(admin_url('admin.php?page=rwpsl-cleaner&cleaned=1&nonce='.wp_create_nonce( 'rwpsl_cleaned_notice' )));
        wp_redirect(admin_url('admin.php?page=rwpsl-cleaner&cleaned=1&notice=success'));
        Helper::terminate();
    }
}
