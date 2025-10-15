<?php
if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * Export 模块的主模板文件
 *
 * @var string $admin_post_url 表单提交地址
 * @var string $nonce_action Export 操作的 nonce action
 * @var array $post_types 可导出的文章类型数组
 * @var boolean $is_feature_enabled 功能是否已启用
 * @var boolean $upgrade_url
 */
?>
<div class="wrap">
    <h1><?php esc_html_e('Export Page View Data', 'rw-postviewstats-lite'); ?></h1>
    <p><?php esc_html_e('Choose the post type to export view data as CSV.', 'rw-postviewstats-lite'); ?></p>

    <form method="post" action="<?php echo esc_url($admin_post_url); ?>">
        <?php wp_nonce_field($nonce_action, 'rwpsl_export_nonce'); ?>
        <input type="hidden" name="action" value="rwpsl_export_csv">

        <table class="form-table">
            <tr>
                <th scope="row"><?php esc_html_e('Post Type', 'rw-postviewstats-lite'); ?></th>
                <td>
                    <select name="post_type" id="post_type">
                        <?php foreach ($post_types as $type): ?>
                            <?php
                            $is_pro_only = !in_array($type->name,['post','page']);
                            $label = esc_html($type->label);
                            if ($is_pro_only) {
                                $label .= ' (Pro Only)';
                            }
                            ?>
                            <option value="<?php echo esc_attr($type->name); ?>"
                                <?php echo $is_pro_only ? 'disabled style="color: #999;" title="'.esc_attr__('Pro Only - Upgrade to use this post type','rw-postviewstats-lite').'"' : ''; ?>>
                                <?php echo esc_html($label); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <p class="description" style="margin-top: 10px;">
                        <a href="<?php echo esc_url($upgrade_url);?>" target="_blank" style="color: #0073aa; font-weight: bold;">
                            <?php esc_html_e('Upgrade to Pro to unlock all post types', 'rw-postviewstats-lite'); ?>
                        </a>
                    </p>
                </td>
            </tr>
        </table>

        <p>
            <input type="submit" id="submit" class="button button-primary" value="<?php esc_attr_e('Export CSV', 'rw-postviewstats-lite'); ?>">
        </p>
    </form>
</div>