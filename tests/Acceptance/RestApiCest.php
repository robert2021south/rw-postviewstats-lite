<?php
namespace Tests\Acceptance;

use RobertWP\PostViewStatsLite\Admin\Settings\SettingsRegistrar;
use RobertWP\PostViewStatsLite\Modules\Tracker\Tracker;
use Tests\Support\AcceptanceTester;

class RestApiCest{

    public function _before(AcceptanceTester $I): void
    {
        // 确保插件启用
        activate_plugin('rw-postviewstats-lite/rw-postviewstats-lite.php');

        // 打开 REST API 开关
        $data = [];
        $data['stat_enabled'] = 1;
        $data['sort_enabled'] = 1;
        $data['rest_api_enabled'] = 1;
        $data['delete_data_on_uninstall'] = 0;
        update_option( SettingsRegistrar::OPTION_SITE_SETTINGS, $data );


        // 刷新 rewrite
        flush_rewrite_rules();
    }

    /**
     * 测试 REST API 返回文章浏览量
     *
     * @param AcceptanceTester $I
     */
    public function testRestApiReturnsPostViews(AcceptanceTester $I): void
    {
        // Step 1: 开启 REST API
        //前置条件
        $data = [];
        $data['stat_enabled'] = 1;
        $data['sort_enabled'] = 1;
        $data['rest_api_enabled'] = 1;
        $data['delete_data_on_uninstall'] = 0;
        update_option( SettingsRegistrar::OPTION_SITE_SETTINGS, $data );

        // Step 2: 创建文章
        $postId = $I->havePostInDatabase([
            'post_title'   => 'REST API Test Post',
            'post_content' => 'Just a test post for REST API.',
            'post_status'  => 'publish',
        ]);

        // Step 3: 设置浏览量
        $I->havePostMetaInDatabase($postId, Tracker::RWPSL_META_KEY_TOTAL, 42);

        // Step 4: REST API URL
        //$url = "/wp-json/rwpsl/v1/views/{$postId}";
        $url = '/index.php?rest_route=/rwpsl/v1/views/'.$postId;

        // Step 5: 访问 REST API
        $I->amOnPage($url);

        // Step 6: 获取响应 JSON
        //$response = $I->grabPageSource();

        // Step 7: 验证返回内容包含正确 post_id 和 views
        //$I->see((string)$postId, $response);
        //$I->see('42', $response);

        $I->see((string)$postId );
        $I->see('42' );


        // Step 8: 测试带 days 参数
        //$I->amOnPage($url . '?days=30');

        //$response2 = $I->grabPageSource();
        //$I->see((string)$postId, $response2);
        //$I->see('42', $response2);

        //$I->see((string)$postId );
        //$I->see('42' );

    }

    /**
     * 测试 REST API 请求不存在文章返回 404
     *
     * @param AcceptanceTester $I
     */
    public function testRestApiReturns404ForInvalidPost(AcceptanceTester $I): void
    {
        //$url = '/wp-json/rwpsl/v1/views/999999';
        $url = '/index.php?rest_route=/rwpsl/v1/views/999999';

        $I->amOnPage($url);

        //$response = $I->grabPageSource();
        // 验证包含错误 code
        //$I->see('invalid_post', $response);
        //$I->see('invalid_post');
        // 检查返回内容包含 "invalid_post"
        $I->seeInSource('"code":"invalid_post"');
    }

}