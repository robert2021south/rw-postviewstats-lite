<?php

namespace Tests\Acceptance;

use RobertWP\PostViewStatsLite\Modules\Tracker\Tracker;
use Tests\Support\AcceptanceTester;

class TrackerAjaxCest
{

    public function _before(AcceptanceTester $I): void
    {
        $_COOKIE = [];
    }

    public function testTrackViewsAjax(AcceptanceTester $I): void
    {
        //$I->wantTo('Test track_views_ajax via WP AJAX');

        // 1. 创建测试文章
        $postId = $I->havePostInDatabase([
            'post_title'  => 'AJAX Post',
            'post_status' => 'publish',
        ]);

        //$I->resetCookie('rwpsl_viewed_' . $postId);

        // 2. 访问测试页面（触发前端 AJAX 的页面）
        $I->amOnPage("/?p=".$postId); // 文章详情页

        // 等待 AJAX 请求完成，而不是固定 2 秒
        $I->waitForJS("return jQuery.active == 0;", 10);
        $I->wait(5); // 给 AJAX 2秒执行时间

        // 3. 点击触发 AJAX 的按钮
        //$I->click('#track-view-button'); // 替换成实际按钮选择器

        // 4. 等待 AJAX 完成（页面 DOM 更新）
        //$I->waitForElementVisible('.success-msg', 10); // 替换成 AJAX 返回显示的元素

        // 5. 断言页面上显示 AJAX 成功
        //$I->see('View tracked!', '.success-msg');

        // 6. 验证数据库中 meta 值已更新
        $I->seePostMetaInDatabase([
            'post_id'    => $postId,
            'meta_key'   => Tracker::RWPSL_META_KEY_TOTAL,
            'meta_value' => 1,
        ]);
    }
}
