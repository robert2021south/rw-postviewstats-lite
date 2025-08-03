<?php
namespace RobertWP\PostViewStatsLite\Admin\Settings;

if (!defined('ABSPATH')) exit;

class SettingsHandler {

    public function after_settings_saved( $old_value, $new_value ) {
        do_action( 'rwpsl_settings_saved', $new_value, $old_value );
    }

    public function handle_settings_form() {

        // 1. 忽略非表单提交的请求
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        // 2. 权限验证
        if (!current_user_can('manage_options')) {
            wp_redirect(admin_url('admin.php?page=rwpsl-settings&notice=ins_perm'));
            exit;
        }

        // 3. Nonce 验证
        if ( empty($_POST['_wpnonce']) || !wp_verify_nonce( $_POST['_wpnonce'], 'rwpsl_save_settings_action' ) ) {
            wp_redirect(network_admin_url('admin.php?page=rwpsl-settings&notice=inv_nonce'));
            exit;
        }

        // 4. 获取并保存设置
        $data = [];
        $data['stat_enabled'] = !empty( $_POST['stat_enabled'] ) ? 1 : 0;
        $data['sort_enabled'] = !empty( $_POST['sort_enabled'] ) ? 1 : 0;
        $data['rest_api_enabled'] = !empty( $_POST['rest_api_enabled'] ) ? 1 : 0;
        $data['delete_data_on_uninstall'] = !empty( $_POST['delete_data_on_uninstall'] ) ? 1 : 0;

        update_option( SettingsRegistrar::OPTION_SITE_SETTINGS, $data );

        // 5. 重定向（防止刷新导致重复提交）
        wp_redirect(admin_url('admin.php?page=rwpsl-settings&notice=success&context=settings'));
        exit;


    }

    public static function sanitize_callback($input) {
        // 执行字段数据清理
        return sanitize_text_field($input);
    }


}