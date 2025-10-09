<?php
namespace RobertWP\PostViewStatsLite\Admin\UI;

use RobertWP\PostViewStatsLite\Core\CallbackWrapper;
use RobertWP\PostViewStatsLite\Utils\TemplateLoader;

class AdminNotice {
    private static bool $conflict_notice_shown = false;
    private static bool $general_notice_registered = false;

    public static function maybe_add_notice(): void
    {
        self::maybe_show_general_notice();
    }

    public static function maybe_show_general_notice(): void
    {
        if (self::$general_notice_registered) return;

        $key = sanitize_text_field( wp_unslash( $_GET['notice'] ?? '' ) );
        $context = sanitize_key( wp_unslash( $_GET['context'] ?? 'common' ) );

        if (empty($key)) return;

        $notices = self::get_notice_definitions();

        // 查找对应消息
        $notice_key = "{$context}:{$key}"; // 例：settings:success
        $default_key = "common:{$key}";

        $notice_data = $notices[$notice_key] ?? $notices[$default_key] ?? null;
        if (!$notice_data) return;

        $custom_message = isset($_GET['msg']) ? sanitize_text_field(wp_unslash($_GET['msg'])) : null;

        $message = $custom_message ?: $notice_data['message'];
        $type = $notice_data['type'] ?? 'warning';

        $callback = CallbackWrapper::plugin_context_only(function () use ($message, $type) {
            TemplateLoader::load('partials/admin-notice-generic', [
                'message' => $message,
                'notice_type' => $type
            ]);
        });

        add_action('admin_notices', $callback);

        self::$general_notice_registered = true;
    }

    private static function get_notice_definitions(): array {
        return [

            // context: settings
            'settings:success' => [
                'type' => 'success',
                'message' => __('Settings saved successfully.', 'rw-postviewstats-lite')
            ],

            //context:  export
            'export:no_posts' => [
                'message' => __('No posts found to export.', 'rw-postviewstats-lite'),
                'type' => 'warning'
            ],

            // common context
            'common:success' => [
                'type' => 'success',
                'message' => __('Operation completed successfully.', 'rw-postviewstats-lite')
            ],
            'common:failure' => [
                'type' => 'error',
                'message' => __('Operation failed. Please try again.', 'rw-postviewstats-lite')
            ],
            'common:pro_only' => [
                'type' => 'warning',
                'message' => __('<strong>Pro Feature:</strong> This feature is only available in the Pro version.', 'rw-postviewstats-lite')
            ],
            'common:ins_perm' => [
                'message' => __('You do not have sufficient permissions', 'rw-postviewstats-lite'),
                'type' => 'error'
            ],
            'common:inv_req' => [
                'message' => __('Invalid request', 'rw-postviewstats-lite'),
                'type' => 'error'
            ],
            'common:inv_nonce' => [
                'message' => __('Invalid Nonce', 'rw-postviewstats-lite'),
                'type' => 'error'
            ],
            'common:sec_chk_fail' => [
                'message' => __('Security check failed.', 'rw-postviewstats-lite'),
                'type' => 'error'
            ],
            'common:unc_exce' => [
                'message' => __('Uncaught exception.', 'rw-postviewstats-lite'),
                'type' => 'error'
            ],


        ];
    }
}
