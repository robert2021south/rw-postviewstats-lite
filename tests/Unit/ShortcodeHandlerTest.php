<?php

namespace RobertWP\PostViewStatsLite\Modules\Shortcode;

// 定义 WordPress 缺失的函数 absint()，避免 PHPUnit 报错
if (!function_exists(__NAMESPACE__ . '\absint')) {
    function absint($maybeint): float|int {
        return abs((int)$maybeint);
    }
}

if (!function_exists(__NAMESPACE__ . '\apply_filters')) {
    function apply_filters($tag, $value) {
        return $value;
    }
}

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use RobertWP\PostViewStatsLite\Modules\Shortcode\ShortcodeHandler;


class ShortcodeHandlerTest extends TestCase {

    protected ShortcodeHandler $handler;

    protected function setUp(): void {
        $this->handler = new ShortcodeHandler();
    }

    // 测试 sanitize_post_id()
    public function test_sanitize_post_id() {
        $reflection = new \ReflectionClass($this->handler);
        $method = $reflection->getMethod('sanitize_post_id');
        $method->setAccessible(true);

        // 正整数
        $this->assertSame(123, $method->invoke($this->handler,123));
        // 字符串数字
        $this->assertSame(45, $method->invoke($this->handler,'45'));
        // 负数 -> 取绝对值
        $this->assertSame(10, $method->invoke($this->handler,-10));
        // 非数字 -> 返回0
        $this->assertSame(0, $method->invoke($this->handler,'abc'));
        // 空值
        $this->assertSame(0, $method->invoke($this->handler,null));
    }

    // 测试 generate_output()（mock apply_filters）
    public function test_generate_output_returns_correct_string() {
        $post_id = 123;

        // 使用 Reflection 调用私有方法
        $reflection = new ReflectionClass($this->handler);
        $method = $reflection->getMethod('generate_output');
        $method->setAccessible(true);

        // 模拟 get_total_views() 返回值
        $handlerMock = $this->getMockBuilder(ShortcodeHandler::class)
            ->onlyMethods(['get_total_views'])
            ->getMock();
        $handlerMock->method('get_total_views')->willReturn(100);

        // mock apply_filters 全局函数（PHPUnit 里可用 runkit 或 patchwork，或者假设 apply_filters 返回原值）
        $output = $method->invoke($handlerMock, $post_id, ['post_id' => $post_id]);
        $this->assertSame('100', $output);
    }
}
