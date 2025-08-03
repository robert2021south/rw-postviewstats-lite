<?php
namespace RobertWP\PostViewStatsLite\I18n;

if (!defined('ABSPATH')) exit;

class Localization {

    public static function load_textdomain() {
        load_plugin_textdomain(
            'rw-postviewstats-lite',
            false,
            dirname(plugin_basename(RWPSL_PLUGIN_FILE)) . '/languages'
        );
    }

}
