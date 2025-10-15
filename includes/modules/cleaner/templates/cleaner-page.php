<?php
if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * Cleaner 模块的主模板文件
 *
 * @var string $nonce_action
 * @var string $admin_post_url
 * @var array $post_types
 * @var bool $show_success_notice
 * @var bool $default_limit
 * @var bool $upgrade_url
 */
?>
<div class="wrap">
    <h1><?php esc_html_e('Clear all view count data', 'rw-postviewstats-lite'); ?></h1>

    <?php if ($show_success_notice): ?>
        <div class="notice notice-success is-dismissible">
            <p><?php esc_html_e('Cleanup completed successfully.', 'rw-postviewstats-lite'); ?></p>
        </div>
    <?php endif; ?>

    <form method="post" action="<?php echo esc_url($admin_post_url); ?>">
        <input type="hidden" name="action" value="rwpsl_cleaner">
        <?php wp_nonce_field($nonce_action, 'rwpsl_cleaner_nonce'); ?>

        <table class="form-table">
            <!-- Post Type -->
            <tr>
                <th scope="row">
                    <label for="post_type"><?php esc_html_e('Post Type', 'rw-postviewstats-lite'); ?></label>
                </th>
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
                        <a href="<?php echo esc_attr($upgrade_url);?>" target="_blank" style="color: #0073aa; font-weight: bold;">
                            <?php esc_html_e('Upgrade to Pro to unlock all post types', 'rw-postviewstats-lite'); ?>
                        </a>
                    </p>


                </td>
            </tr>

            <!-- Date Limit -->
            <tr>
                <th scope="row">
                    <label for="date_limit">
                        <?php esc_html_e('Date limit', 'rw-postviewstats-lite'); ?>
                        （YYYYMMDD，<?php esc_html_e('optional', 'rw-postviewstats-lite'); ?>）
                    </label>
                </th>
                <td>
                    <input type="text" name="date_limit" id="date_limit" value="<?php echo esc_attr($default_limit); ?>" readonly />
                    <p class="description" style="color:#c00;">
                        <?php esc_html_e('Lite version always cleans data older than 30 days.', 'rw-postviewstats-lite'); ?>
                    </p>
                </td>
            </tr>
        </table>

        <?php submit_button(__('Clean up now', 'rw-postviewstats-lite')); ?>
    </form>

</div>
