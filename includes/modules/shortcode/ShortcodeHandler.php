<?php
namespace RobertWP\PostViewStatsLite\Modules\Shortcode;

use RobertWP\PostViewStatsLite\Modules\tracker\Tracker;


class ShortcodeHandler{

    /**
     * 主短码处理方法
     *
     * @param array $atts 短码属性
     * @return string HTML输出
     */
    public function display_post_views($atts = []): string
    {
        global $post;

        // 第一步：提取post_id（始终允许，即使后续权限检查失败）
        $post_id = $this->sanitize_post_id($atts['post_id'] ?? $post->ID ?? 0);
        if (!$post_id) return '0';

        // Lite版逻辑：仅传递允许的参数（post_id）
        $lite_atts = ['post_id' => $post_id]; // 显式保留post_id
        return $this->generate_output($post_id, $lite_atts);
    }

    protected function get_total_views($post_id): int
    {
        return (int)get_post_meta($post_id, Tracker::RWPSL_META_KEY_TOTAL, true);
    }

    // === 私有方法 ===

    /**
     * 生成标准输出
     * @param $post_id
     * @param $atts
     * @return string
     */
    private function generate_output($post_id, $atts): string
    {
        $views = $this->get_total_views($post_id);
        return apply_filters(
            'rwpsl_display_views_output',
            $views ? (string)$views : '0',
            $post_id,
            $atts
        );
    }

    // === 工具方法 ===
    private function sanitize_post_id($post_id): int
    {
        return absint($post_id);
    }


}