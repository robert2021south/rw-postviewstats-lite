<?php
namespace RobertWP\PostViewStatsLite\Core;

class VersionChecker{

    public static function check(): void
    {
        // 防止被其他插件或异常环境调用
        if ( ! defined('RWPSL_PLUGIN_VERSION') ) {
            return;
        }

        $saved_version = get_option(OPTION_RWPSL_VERSION);

        if (version_compare($saved_version, RWPSL_PLUGIN_VERSION, '<')) {
            update_option(OPTION_RWPSL_VERSION, RWPSL_PLUGIN_VERSION);
        }
    }
}
