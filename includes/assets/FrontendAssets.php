<?php
namespace RobertWP\PostViewStatsLite\Assets;

class FrontendAssets {

    public static function enqueue(): void
    {
        self::enqueue_styles();
        self::enqueue_scripts();
    }

    public static function enqueue_styles(): void
    {
        wp_register_style('rwpsl-wp-style-min', RWPSL_ASSETS_URL. 'css/rwpsl-wp-style.min.css', [], RWPSL_PLUGIN_VERSION );
        wp_enqueue_style('rwpsl-wp-style-min');
    }

    private static function enqueue_scripts(): void
    {

        $supported_types = apply_filters('rwpsl_supported_post_types', ['post','page']);
        if (!is_singular($supported_types)) return;

        $post_type = get_post_type();

        wp_enqueue_script('rwpsl-tracker-min', RWPSL_PLUGIN_URL . 'assets/js/rwpsl-tracker.min.js', ['jquery'], RWPSL_PLUGIN_VERSION , true);
        wp_localize_script('rwpsl-tracker-min', 'rwpsl_ajax', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'post_id' => get_the_ID(),
            'post_type'    => $post_type,
            'nonce_action' => "rwpsl_add_view_nonce_$post_type",
            'nonce'    => wp_create_nonce('rwpsl_add_view_nonce_'.$post_type),
        ]);
    }

}
