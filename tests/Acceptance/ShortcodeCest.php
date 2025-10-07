<?php
namespace Tests\Acceptance;

use RobertWP\PostViewStatsLite\Modules\Tracker\Tracker;
use Tests\Support\AcceptanceTester;

class ShortcodeCest{
    /**
     * 测试短代码 [rwpsl_post_views] 正常输出
     *
     * @param AcceptanceTester $I
     */
    public function testShortcodeDisplaysPostViews(AcceptanceTester $I): void
    {
        // Step 1: 创建一篇文章并设置浏览量
        $postId = $I->havePostInDatabase([
            'post_title'   => 'Shortcode Test Post',
            'post_content' => 'Just a test post.',
            'post_status'  => 'publish',
        ]);

        // 给这篇文章设置 meta 值（浏览量 = 5）
        $I->havePostMetaInDatabase($postId, Tracker::RWPSL_META_KEY_TOTAL, 5);

        // Step 2: 创建一个页面，内容包含短代码
        $pageId = $I->havePageInDatabase([
            'post_title'   => 'Shortcode Page',
            'post_content' => '[rwpsl_post_views]',
            'post_status'  => 'publish',
        ]);

        // Step 3: 访问该页面
        //$permalink = $I->grabPostPermalinkFromDatabase($pageId);
        //$siteUrl = rtrim(codecept_root_dir('wp'), '/'); // 或者用 WPBrowser 提供的 url 配置
        //$siteUrl = rtrim(codecept_root_dir('wp'), '/'); // 或者用 WPBrowser 提供的 url 配置
        //$permalink = $siteUrl . '/?p='.$postId;
        $I->amOnPage('/?p='.$postId); // 文章详情页 如果你只想写相对路径，直接用 amOnPage()，更简单、推荐。
        //$I->amOnUrl($permalink); //如果你已经有完整的 URL（带 http://），用 amOnUrl()。

        // Step 4: 验证页面中是否显示正确的浏览量
        $I->see('5');
    }
}