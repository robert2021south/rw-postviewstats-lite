<?php
/** @var string $upgrade_url  */
?>
?>
<div class="wrap">
    <h1><?php esc_html_e('Settings', 'rw-postviewstats-lite'); ?></h1>

    <div class="notice notice-warning is-dismissible" style="border-left: 4px solid #ff9800;">
        <p style="font-size:14px;">
            🚀 <strong><?php esc_html_e( 'Upgrade to WP PostViewStat Pro', 'wp-postviewstat-lite' ); ?></strong>
            <?php esc_html_e( 'to unlock multisite stats, date range filtering, full export functionality, and other advanced features!', 'wp-postviewstat-lite' ); ?>
            <a href="<?php echo esc_url($upgrade_url); ?>" class="button button-primary" target="_blank" style="margin-left:10px;">
                <?php esc_html_e( 'Upgrade Now', 'wp-postviewstat-lite' ); ?>
            </a>
        </p>
    </div>

    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
        <input type="hidden" name="action" value="rwpsl_save_settings">
        <?php wp_nonce_field('rwpsl_save_settings_action'); ?>

        <?php //settings_fields('rwpsl_save_settings'); ?>
        <?php do_settings_sections('rwpsl-settings'); ?>
        <?php submit_button(); ?>
    </form>

    <div class="plugin-support-footer" style="margin-top: 2em; padding: 1em; background: #f8f9fa; border-left: 4px solid #2271b1;">
        <h3>❓ <?php _e('Need help? Need help? Follow these steps:', 'rw-postviewstats-lite'); ?></h3>
        <ol>
            <li><strong>📖 <?php _e('Check the', 'rw-postviewstats-lite'); ?> <a href="https://docs.robertwp.com/rw-postviewstats-pro/en/#/guide" target="_blank"><?php _e('[5-Minute Quick Guide]', 'rw-postviewstats-lite'); ?></a></strong>（<?php _e('covers 90% FAQs', 'rw-postviewstats-lite'); ?>）</li>
            <li><strong>🔍 <?php printf(
                        esc_html__('Search %s with keywords', 'rw-postviewstats-lite'),
                        '<a href="https://docs.robertwp.com/rw-postviewstats-pro/en/#/faq" target="_blank">' . esc_html__('[Top Questions]',
                            'rw-postviewstats-lite') . '</a>'
                    ); ?></strong></li>
            <li><strong>✉️ <?php _e('Still stuck?', 'rw-postviewstats-lite'); ?> <a href="mailto:support@robertwp.com?subject=<?php _e('Plugin Issue: {Brief Description}&body=● Details:%0A● Screenshots/Logs:', 'rw-postviewstats-lite'); ?>"><?php _e('Email us', 'rw-postviewstats-lite'); ?></a></strong> (<?php _e('include: problem details + screenshots + error logs', 'rw-postviewstats-lite'); ?>)</li>
        </ol>
        <p><small>⚠️ <em><?php _e('Replies may be delayed if key info is missing.', 'rw-postviewstats-lite'); ?></em></small></p>
    </div>
    
    <div class="lite-pro-compare" id="liteProCompare">
        <div class="lite-pro-header">
            RW PostViewStats Lite / Pro Comparison
            <span class="lite-pro-close" onclick="document.getElementById('liteProCompare').style.display='none'">&times;</span>
        </div>
        <table class="lite-pro-table">
            <tr>
                <th>Feature</th>
                <th>Lite</th>
                <th class="lite-pro-highlight">Pro</th>
            </tr>
            <tr>
                <td>Views Display</td>
                <td>Show views in post/page list; shortcode shows only total views of the current post (can specify post ID)</td>
                <td class="lite-pro-highlight">Shortcode supports date and date range in addition</td>
            </tr>
            <tr>
                <td>REST API</td>
                <td>Get views of a specific post ID only</td>
                <td class="lite-pro-highlight">Support specific days range and manually increase views for a post</td>
            </tr>
            <tr>
                <td>Data Export</td>
                <td>Export only standard posts and pages</td>
                <td class="lite-pro-highlight">Export custom post types</td>
            </tr>
            <tr>
                <td>Data Cleaning</td>
                <td>Clean only standard posts and pages</td>
                <td class="lite-pro-highlight">Clean custom post types and by specific date</td>
            </tr>
            <tr>
                <td>Multisite Support</td>
                <td>✖</td>
                <td class="lite-pro-highlight">✔</td>
            </tr>
        </table>

        <div class="lite-pro-header" style="margin-top:10px;">
            Envato License Plans
        </div>
        <table class="lite-pro-table">
            <tr>
                <th>License</th>
                <th>Regular</th>
                <th class="lite-pro-highlight">Extended</th>
            </tr>
            <tr>
                <td>Sites Allowed</td>
                <td>1 site</td>
                <td class="lite-pro-highlight">Unlimited sites</td>
            </tr>
            <tr>
                <td>Validity</td>
                <td>Lifetime use, 1 year updates</td>
                <td class="lite-pro-highlight">Lifetime use, lifetime updates</td>
            </tr>
            <tr>
                <td>Support</td>
                <td>6 months (extendable to 12 months on Envato)</td>
                <td class="lite-pro-highlight">1 year (extendable on Envato)</td>
            </tr>
        </table>

        <div style="text-align:center; padding:15px;">
            <a href="<?php echo esc_url($upgrade_url);?>"
               target="_blank"
               style="display:inline-block; padding:10px 20px; background:#0073aa; color:#fff; text-decoration:none; border-radius:5px; font-weight:bold;">
                Upgrade to Pro
            </a>
        </div>
    </div>

</div>