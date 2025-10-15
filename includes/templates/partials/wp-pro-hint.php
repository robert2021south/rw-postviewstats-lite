<?php
if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * PRO Admin Hint Template
 *
 * @var int    $basic_views  Total views count
 * @var string $upgrade_url  Upgrade page URL
 * @var string $violated_param  Violated param
 */
?>
<div class="rwpsl-shortcode-wrapper">
    <?php if (!empty($violated_param)) : ?>
        <div class="rwpsl-param-warning">
            <?php /* translators: %s: parameter name that requires PRO license */
            printf(esc_html__('Parameter "%s" requires PRO license', 'rw-postviewstats-lite'), esc_html($violated_param));
            ?>
        </div>
    <?php endif; ?>

    <span class="rwpsl-admin-hint" data-test="pro-hint">
        <?php echo esc_html($basic_views); ?>
        <a href="<?php echo esc_url($upgrade_url); ?>"
           class="rwpsl-admin-hint__badge"
           aria-label="<?php esc_attr_e('Upgrade to PRO', 'rw-postviewstats-lite'); ?>">
           <?php esc_html_e('PRO', 'rw-postviewstats-lite'); ?>
        </a>
        <!-- 新增悬浮提示元素 -->
        <span class="rwpsl-pro-hint">
            <?php esc_html_e('Upgrade to unlock advanced features', 'rw-postviewstats-lite'); ?>
        </span>
    </span>
</div>