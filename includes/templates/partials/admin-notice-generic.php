<?php
/**
 * @var string $message
 * * @var string $notice_type
 */
?>

<?php if ( ! empty( $message ) ) : ?>
    <div class="notice notice-<?php echo esc_attr($notice_type); ?> is-dismissible">
        <p><?php echo esc_html($message); ?></p>
    </div>
<?php endif; ?>
