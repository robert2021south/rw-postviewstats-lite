<div class="wrap">
    <h1><?php esc_html_e('RW PostViewStats Lite Settings', 'rw-postviewstats-lite'); ?></h1>

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
            <li><strong>📖 <?php _e('Check the', 'rw-postviewstats-lite'); ?> <a href="https://docs.robertwp.com/rw-postviewstats-lite/en/#/guide" target="_blank"><?php _e('[5-Minute Quick Guide]', 'rw-postviewstats-lite'); ?></a></strong>（<?php _e('covers 90% FAQs', 'rw-postviewstats-lite'); ?>）</li>
            <li><strong>🔍 <?php printf(
                        esc_html__('Search %s with keywords', 'rw-postviewstats-lite'),
                        '<a href="https://docs.robertwp.com/rw-postviewstats-lite/en/#/faq" target="_blank">' . esc_html__('[Top Questions]',
                            'rw-postviewstats-lite') . '</a>'
                    ); ?></strong></li>
            <li><strong>✉️ <?php _e('Still stuck?', 'rw-postviewstats-lite'); ?> <a href="mailto:support@robertwp.com?subject=<?php _e('Plugin Issue: {Brief Description}&body=● Details:%0A● Screenshots/Logs:', 'rw-postviewstats-lite'); ?>"><?php _e('Email us', 'rw-postviewstats-lite'); ?></a></strong> (<?php _e('include: problem details + screenshots + error logs', 'rw-postviewstats-lite'); ?>)</li>
        </ol>
        <p><small>⚠️ <em><?php _e('Replies may be delayed if key info is missing.', 'rw-postviewstats-lite'); ?></em></small></p>
    </div>
</div>