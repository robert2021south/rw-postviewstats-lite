<?php
namespace Tests\Acceptance;

use Facebook\WebDriver\Remote\RemoteWebDriver;
use RobertWP\PostViewStatsLite\Modules\Tracker\Tracker;
use Tests\Support\AcceptanceTester;

class SettingsCest
{
    public function _before(AcceptanceTester $I): void
    {
        $I->executeInSelenium(function(RemoteWebDriver $webDriver) {
            $webDriver->manage()->deleteAllCookies();
        });

        $I->loginAsAdmin(); // 假设有封装好的登录方法
    }

    public function testToggleStatEnabled(AcceptanceTester $I): void
    {

        // -------------------------
        // 前置：创建文章
        // -------------------------
        $postId = $I->havePostInDatabase([
            'post_title' => 'Test Post for Settings',
            'post_content' => 'Content here',
            'post_status' => 'publish',
        ]);

        // 初始化浏览量
        $I->havePostMetaInDatabase($postId, Tracker::RWPSL_META_KEY_TOTAL, 0);
//        $realValue = $I->grabFromDatabase(
//            'wp_postmeta',
//            'meta_value',
//            ['post_id' => $postId, 'meta_key' => Tracker::RWPSL_META_KEY_TOTAL]
//        );
//
//        codecept_debug("1, 实际 meta_value: " . var_export($realValue, true));

        // -------------------------
        // Step 1: 登录后台
        // -------------------------
        //$I->loginAsAdmin(); // WPBrowser 内置登录方法

        // -------------------------
        // Step 2: 进入插件设置页面
        // -------------------------
        $I->amOnAdminPage('admin.php?page=rwpsl-settings');

        // -------------------------
        // Step 2: 勾选“禁止统计浏览量”并保存  // 禁止统计
        // -------------------------
        // 假设 checkbox 名称为 stat_enabled
        $I->uncheckOption('stat_enabled'); // 禁止统计   先取消，后选中，两次测试

        $I->waitForElementVisible('#submit', 5);
        $I->click('#submit'); // 用 id
        //$I->click('Save Settings'); // 根据实际按钮文本填写

        // 可选：验证保存成功
        $I->seeInCurrentUrl('notice=success');
        //$I->seeInCurrentUrl('context=settings');
        //$I->see('Settings saved', 'div.notice-success');

        // -------------------------
        // Step 3: 访问文章页面 // 再次访问文章 → 浏览量增加
        // -------------------------
        $I->amOnPage("/?p={$postId}"); // 或文章固定链接
        //$viewsBefore = (int)$I->grabTextFrom('#rwpsl_post_views'); // 根据前端渲染元素 id 修改
        $I->wait(2);

        // -------------------------
        // Step 4: 验证浏览量不增加
        // -------------------------
        // 验证浏览量为 0
        $I->seeInDatabase(
            'wp_postmeta',
            [
                'post_id' => $postId,
                'meta_key' => Tracker::RWPSL_META_KEY_TOTAL,
                'meta_value' => 0
            ]
        );

        //清理12小时防重限制cookie
        $I->resetCookie('rwpsl_viewed_' . $postId);

        // ------------------------------------------------------------------------
        // 取消勾选“禁止统计”，再访问文章 允许统计
        // ------------------------------------------------------------------------
        // -------------------------
        // Step 1: 进入插件设置页面
        // -------------------------
        $I->amOnAdminPage('admin.php?page=rwpsl-settings');
        // -------------------------
        // Step 2: 勾选“禁止统计浏览量”并保存
        // -------------------------
        $I->checkOption('stat_enabled'); // 允许统计
        $I->waitForElementVisible('#submit', 5);
        $I->click('#submit'); // 用 id
        //$I->click('Save Settings');

        // 可选：验证保存成功
        $I->seeInCurrentUrl('notice=success');
        //$I->seeInCurrentUrl('context=settings');
        //$I->see('Settings saved', 'div.notice-success');

        // -------------------------
        // Step 3: 访问文章页面
        // -------------------------
        $I->amOnPage("/?p={$postId}"); // 或文章固定链接
        $I->wait(3);

        // -------------------------
        // Step 4: 验证浏览量增加（验证浏览量为 1）
        // -------------------------
        $I->seeInDatabase(
            'wp_postmeta',
            [
                'post_id' => $postId,
                'meta_key' => Tracker::RWPSL_META_KEY_TOTAL,
                'meta_value' => 1
            ]
        );


    }


    public function testToggleSortingEnabled(AcceptanceTester $I): void
    {
//        $I->executeInSelenium(function(RemoteWebDriver $webDriver) {
//            $webDriver->manage()->deleteAllCookies();
//        });

        // 前置：创建两篇文章
        $postId1 = $I->havePostInDatabase([
            'post_title' => 'Sort Post 1',
            'post_status' => 'publish',
        ]);
        $postId2 = $I->havePostInDatabase([
            'post_title' => 'Sort Post 2',
            'post_status' => 'publish',
        ]);

        // 设置浏览量：Post1 > Post2
        $I->havePostMetaInDatabase($postId1, Tracker::RWPSL_META_KEY_TOTAL, 5);
        $I->havePostMetaInDatabase($postId2, Tracker::RWPSL_META_KEY_TOTAL, 10);

        // Step 1: 登录后台，关闭排序
        //$I->loginAsAdmin();
        $I->amOnAdminPage('admin.php?page=rwpsl-settings');

        $I->uncheckOption('sort_enabled');

        $I->click('#submit');

        $I->seeInCurrentUrl('notice=success');

        // Step 2: 后台访问文章列表页，确认“按浏览量排序”不可用
        $I->amOnAdminPage('edit.php?orderby=views&order=desc');
        $I->dontSeeElement('a[href*="orderby=views"]');// 断言不可排序

        // Step 3: 启用排序
        $I->amOnAdminPage('admin.php?page=rwpsl-settings');
        $I->checkOption('sort_enabled');
        $I->click('#submit');
        $I->seeInCurrentUrl('notice=success');

        // Step 4: 再次访问归档页，检查按浏览量排序生效
        $I->amOnAdminPage('edit.php?orderby=views&order=desc');
        $I->seeElement('a[href*="orderby=views"]');// 断言可排序

        // 假设归档页渲染顺序，断言文章顺序正确
//        $pageSource = $I->grabPageSource();
//        $pos1 = strpos($pageSource, 'Sort Post 1');
//        $pos2 = strpos($pageSource, 'Sort Post 2');

        //$I->assertTrue($pos2 > $pos1, 'Sort Post 2 should appear before Sort Post 1 in DESC order');

    }

    public function testToggleRestApiEnabled(AcceptanceTester $I): void
    {
//        $I->executeInSelenium(function(RemoteWebDriver $webDriver) {
//            $webDriver->manage()->deleteAllCookies();
//        });

        // 前置：创建一篇文章
        $postId = $I->havePostInDatabase([
            'post_title' => 'API Post',
            'post_status' => 'publish',
        ]);
        $I->havePostMetaInDatabase($postId, Tracker::RWPSL_META_KEY_TOTAL, 0);

        // Step 1: 登录后台，关闭 REST API
        //$I->loginAsAdmin();
        $I->amOnAdminPage('admin.php?page=rwpsl-settings');

        $I->uncheckOption('rest_api_enabled');

        $I->click('#submit');

        $I->seeInCurrentUrl('notice=success');

        // Step 2: 尝试访问 REST API，应失败
        $I->amOnPage("/?rest_route=/rwpsl/v1/views/{$postId}/");
        $I->dontSee('API Post'); // 根据实际输出调整
        $I->see('rest_no_route'); // 插件可返回错误标识（视你的实现情况）

        // Step 3: 启用 REST API
        $I->amOnAdminPage('admin.php?page=rwpsl-settings');
        $I->checkOption('rest_api_enabled');
        $I->click('#submit');
        $I->seeInCurrentUrl('notice=success');

        // Step 4: 再次访问 REST API，应成功
        $I->amOnPage("/?rest_route=/rwpsl/v1/views/{$postId}/");
        $I->see('post_id');
    }

}
