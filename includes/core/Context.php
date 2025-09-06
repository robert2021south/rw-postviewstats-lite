<?php
namespace RobertWP\PostViewStatsLite\Core;

class Context
{
    public static function is_plugin_context(): bool
    {
        global $pagenow;

        if (is_admin()) {
            $page = wp_unslash($_GET['page'] ?? '');
            // 1. 检查是否在后台文章/页面/自定义类型列表页
            if ($pagenow === 'edit.php') {
                return true;
            }
            // 2. 检查插件专属页面（如 ?page=rwpsl...）
            if (strpos($page, 'rwpsl') === 0) {
                return true;
            }
        }

        // 3. 检查插件专属 AJAX/REST 操作
        $action = wp_unslash( $_REQUEST['action'] ?? '' );
        if (strpos($_REQUEST['action'], 'rwpsl_') === 0) {
            return true;
        }

        $uri = wp_unslash( $_SERVER['REQUEST_URI'] ?? '' );
        if (defined('REST_REQUEST') && REST_REQUEST && strpos($uri ?? '', '/wp-json/rwpsl/') !== false) {
            return true;
        }

        return false;
    }
}

