<?php
namespace RobertWP\PostViewStatsLite\Utils;

class TemplateLoader
{
    protected static string $plugin_base_path = '';

    public static function init(string $base_path): void
    {
        self::$plugin_base_path = rtrim($base_path, '/\\') . '/';
    }

    public static function render(string $template_name, array $args = [], string $module = ''): string
    {
        $template_file = self::locate($template_name, $module);

        if ($template_file) {
            extract($args, EXTR_SKIP);
            ob_start();
            include $template_file;
            return ob_get_clean();
        }

        return '';
    }

    /**
     * 加载模板
     *
     * @param string $template_name 模板名（不带路径，如 'export-settings'）
     * @param array $args 传给模板的数据（可选）
     * @param string $module 所属模块名（如 'export'，可选）
     */
    public static function load(string $template_name, array $args = [], string $module = ''): void
    {

        $template_file = self::locate($template_name, $module);

        if ($template_file) {
            extract($args, EXTR_SKIP);
            include $template_file;
        } else {
            Helper::rw_error_log("Template '{$template_name}' not found.");
        }
    }

    /**
     * 返回模板的实际路径（优先模块内部，再查全局模板目录）
     *
     * @param string $template_name
     * @param string $module
     * @return string|null
     */
    protected static function locate(string $template_name, string $module = ''): ?string
    {
        $filename = $template_name . '.php';

        // 1. 模块私有模板
        if ($module) {
            $module_path = self::$plugin_base_path . 'includes/modules/' . $module . '/templates/' . $filename;
            if (file_exists($module_path)) {
                return $module_path;
            }
        }

        // 2. 全局模板目录
        $normalized = str_replace(['..', '//'], '', $template_name);
        $global_path = self::$plugin_base_path . 'includes/templates/' . $normalized . '.php';

        if (file_exists($global_path)) {
            return $global_path;
        }
        return null;
    }
}
