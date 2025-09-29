<?php
namespace RobertWP\PostViewStatsLite\I18n;

class Localization {

    public static function load_textdomain(): void
    {
        load_plugin_textdomain(
            'rw-postviewstats-lite',
            false,
            dirname(plugin_basename(RWPSL_PLUGIN_FILE)) . '/languages'
        );
    }

}
