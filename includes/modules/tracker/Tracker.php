<?php
/*
 * */
namespace RobertWP\PostViewStatsLite\Modules\Tracker;

use RobertWP\PostViewStatsLite\Admin\Settings\SettingsRegistrar;

class Tracker {

    const RWPSL_META_KEY_TOTAL = '_rwpsl_total' ;
    const RWPSL_META_KEY_TODAY_PREFIX = '_rwpsl_today_';

    public function track_views_ajax() {

        $post_id = absint($_POST['post_id'] ?? 0);
        $nonce = sanitize_text_field( wp_unslash( $_POST['nonce'] ?? '' ) );
        $nonce_action = sanitize_text_field( wp_unslash( $_POST['nonce_action'] ?? '' ) );

        if (!$post_id) wp_send_json_error();

        //  // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        if (empty($nonce) || !wp_verify_nonce($nonce, $nonce_action)) {
            wp_send_json_error('Nonce verification failed');
            return;
        }

        $this->track_views($post_id); // Same processing logic
        wp_send_json_success(['ok' => true]);
    }

    public function track_views($post_id) {
        // Check whether to turn on page view statistics.
        if ( SettingsRegistrar::get_effective_setting('stat_enabled') !== 1 ) {
            return;
        }

        $should_count = apply_filters( 'rwpsl_should_count_view', true, $post_id );
        if ( ! $should_count ) {
            return;
        }


        // Total views
        $old_view_count = (int) get_post_meta($post_id, self::RWPSL_META_KEY_TOTAL, true);
        $new_view_count = $old_view_count + 1;
        update_post_meta($post_id, self::RWPSL_META_KEY_TOTAL, $new_view_count);

        // Today's views
        $today_key = self::RWPSL_META_KEY_TODAY_PREFIX . gmdate('Ymd');
        $today = (int) get_post_meta($post_id, $today_key, true);
        update_post_meta($post_id, $today_key, $today + 1);

        do_action( 'rwpsl_post_view_count_updated', $post_id, $new_view_count, $old_view_count );

    }

    /**
     * Retrieve post views by ID
     * @param $post_id
     * @return int
     */
    public static function get_views($post_id) {
        return (int) get_post_meta($post_id, self::RWPSL_META_KEY_TOTAL, true);
    }

}
