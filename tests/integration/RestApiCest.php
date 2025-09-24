<?php
namespace Tests\Integration;

use RobertWP\PostViewStatsLite\Modules\RestApi\RestApi;
use RobertWP\PostViewStatsLite\Modules\Tracker\Tracker;
use Tests\Support\IntegrationTester;

class RestApiCest
{
    /**
     * 测试：文章不存在时返回错误
     */
    public function test_returns_error_if_post_not_found(IntegrationTester $I): void
    {
        $request = ['id' => 99999]; // 一个不存在的文章 ID
        $response = RestApi::get_post_views($request);

        $I->assertInstanceOf(\WP_Error::class, $response);
        $I->assertEquals('invalid_post', $response->get_error_code());
    }

    /**
     * 测试：文章不是已发布状态时返回错误
     */
    public function test_returns_error_if_post_is_not_published(IntegrationTester $I): void
    {
        // 创建一篇草稿文章
        $post_id = wp_insert_post(['post_status' => 'draft']);

        $request = ['id' => $post_id];
        $response = RestApi::get_post_views($request);

        $I->assertInstanceOf(\WP_Error::class, $response);
        $I->assertEquals(404, $response->get_error_data()['status']);
    }

    /**
     * 测试：文章已发布时返回浏览量
     */
    public function test_returns_post_views_if_post_is_published(IntegrationTester $I): void
    {
        // 创建一篇发布文章
        $post_id = wp_insert_post([
            'post_title'   => 'Test Post',
            'post_content' => 'Some test content',
            'post_status'  => 'publish',
            'post_type'    => 'post',
        ]);

        $I->assertGreaterThan(0, $post_id, 'Post ID should be valid');

        // 如果你的 get_views 函数依赖真实数据，可以事先设置 meta 或直接使用真实逻辑
        // 这里假设 get_views 返回 99，和你原来的测试一致
        update_post_meta($post_id, Tracker::RWPSL_META_KEY_TOTAL, 99);

        $request = ['id' => $post_id, 'days' => 7];
        $response = RestApi::get_post_views($request);
        $I->assertIsArray($response);
        $I->assertEquals($post_id, $response['post_id']);
        $I->assertEquals(99, $response['views']);
    }

}
