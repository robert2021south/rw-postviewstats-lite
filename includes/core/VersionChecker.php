<?php
namespace RobertWP\PostViewStatsLite\Core;

class VersionChecker{

    public static function check()
    {
        // 防止被其他插件或异常环境调用
        if ( ! defined('RWPSL_PLUGIN_VERSION') ) {
            return;
        }

        $saved_version = get_option(RWPSL_VERSION_OPTION);

        if (version_compare($saved_version, RWPSL_PLUGIN_VERSION, '<')) {
            update_option(RWPSL_VERSION_OPTION, RWPSL_PLUGIN_VERSION);
        }
    }
}
