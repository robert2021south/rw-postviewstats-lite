<?php
namespace Tests\Acceptance;

use Tests\Support\AcceptanceTester;

class CleanerCest{

    /**
     * 前置准备：管理员登录
     */
//    public function _before(AcceptanceTester $I): void
//    {
//        //$I->loginAsAdmin(); // 假设有封装好的登录方法
//    }
//    public function _before(AcceptanceTester $I): void
//    {
//        $I->amOnPage('/wp-login.php');
//        $I->fillField('log', 'admin');
//        $I->fillField('pwd', 'pBlaWDphJvFab5Jbi3KR9q6s');
//        $I->click('Log In');
//        //$I->waitForElementVisible('#wpadminbar', 20);
//    }

    public function _before(AcceptanceTester $I): void
    {
        // 等待WordPress完全启动
        $I->waitForElementVisible('body', 30);

        // 登录前等待登录页面加载完成
        $I->amOnPage('/wp-login.php');
        $I->waitForElement('#loginform', 30);
        $I->see('Log In');

        $I->fillField('log', 'admin');
        $I->fillField('pwd', 'pBlaWDphJvFab5Jbi3KR9q6s');
        $I->click('wp-submit');

        // 登录后等待重定向
        $I->waitForElement('#adminmenu', 30);
    }

    /**
     * 1. 管理员清理文章数据  post 清理
     */
    public function cleanPosts(AcceptanceTester $I): void
    {
        $post_id = $I->havePostInDatabase([
            'post_title' => '测试文章',
            'post_type'  => 'post',
            'post_status'=> 'publish',
        ]);

        $today = date('Ymd');
        $old_date = date('Ymd', strtotime('-31 days'));

        // 插入 meta
        $I->haveInDatabase('wp_postmeta', [
            'post_id'  => $post_id,
            'meta_key' => '_rwpsl_today_' . $today,
            'meta_value'=> 5,
        ]);
        $I->haveInDatabase('wp_postmeta', [
            'post_id'  => $post_id,
            'meta_key' => '_rwpsl_today_' . $old_date,
            'meta_value'=> 3,
        ]);
        $I->haveInDatabase('wp_postmeta', [
            'post_id'  => $post_id,
            'meta_key' => '_rwpsl_total',
            'meta_value'=> 8,
        ]);

        // 打开清理页面并选择 post 类型
        $I->amOnAdminPage('admin.php?page=rwpsl-cleaner');
        $I->selectOption('select[name=post_type]', 'post');
        $I->click('#submit');

        // 验证跳转
        $I->seeInCurrentUrl('page=rwpsl-cleaner&cleaned=1');

        // 验证数据库
        $I->dontSeeInDatabase('wp_postmeta', [
            'post_id'  => $post_id,
            'meta_key' => '_rwpsl_today_' . $old_date,
        ]);
        $I->seeInDatabase('wp_postmeta', [
            'post_id'  => $post_id,
            'meta_key' => '_rwpsl_today_' . $today,
            'meta_value'=> 5,
        ]);
        $I->seeInDatabase('wp_postmeta', [
            'post_id'  => $post_id,
            'meta_key' => '_rwpsl_total',
            'meta_value'=> 5,
        ]);
    }

    /**
     * 2. 管理员清理页面数据 page 清理
     */
    public function cleanPages(AcceptanceTester $I): void
    {
        $page_id = $I->havePostInDatabase([
            'post_title' => '测试页面',
            'post_type'  => 'page',
            'post_status'=> 'publish',
        ]);

        $today = date('Ymd');

        $I->haveInDatabase('wp_postmeta', [
            'post_id'  => $page_id,
            'meta_key' => '_rwpsl_today_' . $today,
            'meta_value'=> 7,
        ]);
        $I->haveInDatabase('wp_postmeta', [
            'post_id'  => $page_id,
            'meta_key' => '_rwpsl_total',
            'meta_value'=> 7,
        ]);

        $I->amOnAdminPage('admin.php?page=rwpsl-cleaner');
        $I->selectOption('select[name=post_type]', 'page');
        $I->click('#submit');

        $I->seeInCurrentUrl('page=rwpsl-cleaner&cleaned=1');

        $I->seeInDatabase('wp_postmeta', [
            'post_id'  => $page_id,
            'meta_key' => '_rwpsl_today_' . $today,
            'meta_value'=> 7,
        ]);
        $I->seeInDatabase('wp_postmeta', [
            'post_id'  => $page_id,
            'meta_key' => '_rwpsl_total',
            'meta_value'=> 7,
        ]);
    }

    /**
     * 3. 非法 post_type 回退为 post
     */
    public function fallbackInvalidPostType(AcceptanceTester $I): void
    {
        $post_id = $I->havePostInDatabase([
            'post_title' => '非法类型测试文章',
            'post_type'  => 'post',
            'post_status'=> 'publish',
        ]);

        $today = date('Ymd');
        $I->haveInDatabase('wp_postmeta', [
            'post_id'  => $post_id,
            'meta_key' => '_rwpsl_today_' . $today,
            'meta_value'=> 4,
        ]);
        $I->haveInDatabase('wp_postmeta', [
            'post_id'  => $post_id,
            'meta_key' => '_rwpsl_total',
            'meta_value'=> 4,
        ]);

        $I->amOnAdminPage('admin.php?page=rwpsl-cleaner');
        $I->selectOption('select[name=post_type]', 'post');
        $I->executeJS("document.querySelector('select[name=post_type]').value = 'invalid_type';");
        $I->click('#submit');

        $I->seeInCurrentUrl('page=rwpsl-cleaner&cleaned=1');

        $I->seeInDatabase('wp_postmeta', [
            'post_id'  => $post_id,
            'meta_key' => '_rwpsl_today_' . $today,
            'meta_value'=> 4,
        ]);
        $I->seeInDatabase('wp_postmeta', [
            'post_id'  => $post_id,
            'meta_key' => '_rwpsl_total',
            'meta_value'=> 4,
        ]);
    }

    /**
     * 4. 仅保留有效数据 → total 被删除
     */
    public function deleteTotalWhenNoValidData(AcceptanceTester $I): void
    {
        $post_id = $I->havePostInDatabase([
            'post_title' => '过期数据文章',
            'post_type'  => 'post',
            'post_status'=> 'publish',
        ]);

        $old_date = date('Ymd', strtotime('-31 days'));
        $I->haveInDatabase('wp_postmeta', [
            'post_id'  => $post_id,
            'meta_key' => '_rwpsl_today_' . $old_date,
            'meta_value'=> 3,
        ]);
        $I->haveInDatabase('wp_postmeta', [
            'post_id'  => $post_id,
            'meta_key' => '_rwpsl_total',
            'meta_value'=> 3,
        ]);

        $I->amOnAdminPage('admin.php?page=rwpsl-cleaner');
        $I->selectOption('select[name=post_type]', 'post');
        $I->click('#submit');

        $I->seeInCurrentUrl('page=rwpsl-cleaner&cleaned=1');

        $I->dontSeeInDatabase('wp_postmeta', [
            'post_id'  => $post_id,
            'meta_key' => '_rwpsl_today_' . $old_date,
        ]);
        $I->dontSeeInDatabase('wp_postmeta', [
            'post_id'  => $post_id,
            'meta_key' => '_rwpsl_total',
        ]);
    }

    /**
     * 5. 有部分有效数据 → total 更新
     */
    public function updateTotalWithValidData(AcceptanceTester $I): void
    {
        $post_id = $I->havePostInDatabase([
            'post_title' => '部分有效数据文章',
            'post_type'  => 'post',
            'post_status'=> 'publish',
        ]);

        $today = date('Ymd');
        $old_date = date('Ymd', strtotime('-31 days'));

        $I->haveInDatabase('wp_postmeta', [
            'post_id'  => $post_id,
            'meta_key' => '_rwpsl_today_' . $today,
            'meta_value'=> 5,
        ]);
        $I->haveInDatabase('wp_postmeta', [
            'post_id'  => $post_id,
            'meta_key' => '_rwpsl_today_' . $old_date,
            'meta_value'=> 3,
        ]);
        $I->haveInDatabase('wp_postmeta', [
            'post_id'  => $post_id,
            'meta_key' => '_rwpsl_total',
            'meta_value'=> 8,
        ]);

        $I->amOnAdminPage('admin.php?page=rwpsl-cleaner');
        $I->selectOption('select[name=post_type]', 'post');
        $I->click('#submit');

        $I->seeInCurrentUrl('page=rwpsl-cleaner&cleaned=1');

        $I->dontSeeInDatabase('wp_postmeta', [
            'post_id'  => $post_id,
            'meta_key' => '_rwpsl_today_' . $old_date,
        ]);
        $I->seeInDatabase('wp_postmeta', [
            'post_id'  => $post_id,
            'meta_key' => '_rwpsl_today_' . $today,
            'meta_value'=> 5,
        ]);
        $I->seeInDatabase('wp_postmeta', [
            'post_id'  => $post_id,
            'meta_key' => '_rwpsl_total',
            'meta_value'=> 5,
        ]);
    }
}