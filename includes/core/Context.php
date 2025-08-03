<?php
namespace RobertWP\PostViewStatsLite\Core;

if (!defined('ABSPATH')) exit;

class Context
{
    public static function is_plugin_context(): bool
    {
        global $pagenow;

        // 1. 检查是否在后台文章/页面/自定义类型列表页
        if (is_admin() && $pagenow === 'edit.php') {
            return true;
        }

        // 2. 检查插件专属页面（如 ?page=rwpsl...）
        if (is_admin() && isset($_GET['page']) && strpos($_GET['page'], 'rwpsl') === 0) {
            return true;
        }

        // 3. 检查插件专属 AJAX/REST 操作
        if (isset($_REQUEST['action']) && strpos($_REQUEST['action'], 'rwpsl_') === 0) {
            return true;
        }

        if (defined('REST_REQUEST') && REST_REQUEST && strpos($_SERVER['REQUEST_URI'] ?? '', '/wp-json/rwpsl/') !== false) {
            return true;
        }

        return false;
    }
}

