<?php
namespace RobertWP\PostViewStatsLite\Assets;

if (!defined('ABSPATH')) exit;

class AdminAssets {

    public static function enqueue() {
        self::enqueue_styles();
    }

    private static function enqueue_styles() {
        wp_register_style('rwpsl-admin-style-min', RWPSL_ASSETS_URL. 'css/rwpsl-admin-style.min.css', [], RWPSL_PLUGIN_VERSION );
        wp_enqueue_style('rwpsl-admin-style-min');
    }

}
