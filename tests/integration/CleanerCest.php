<?php

namespace Tests\Integration;

use RobertWP\PostViewStatsLite\Modules\Cleaner\Cleaner;
use RobertWP\PostViewStatsLite\Modules\Tracker\Tracker;
use Tests\Support\IntegrationTester;

class CleanerCest
{

    public function _before(IntegrationTester $I): void
    {
        // 每个测试运行前清除 WordPress 对象缓存
        wp_cache_flush();
    }

    public function _after(IntegrationTester $I): void
    {
        // 每个测试运行后再清一次，确保干净
        wp_cache_flush();
    }

    /**
     * 权限不足时跳转
     */
    public function testRedirectsWhenUserCannotManageOptions(IntegrationTester $I): void
    {
        // 模拟未登录用户
        wp_set_current_user(0);

        $_POST = []; // 空请求

        //只捕获第一次 wp_redirect
        add_filter('wp_redirect', function($location) use (&$captured) {
            if ($captured === null) {
                $captured = $location;
            }
            return $location;
        });

        try {
            Cleaner::handle_cleaner_request();
        } catch (\Throwable $e) {
            // 捕获 wp_die()
        }

        $I->assertStringContainsString('notice=ins_perm', $captured);
    }

    /**
     * Nonce 无效时跳转
     */
    public function testRedirectsWhenNonceInvalid(IntegrationTester $I): void
    {
        // 创建一个管理员用户
        $user_id = $I->haveUserInDatabase('admin', 'administrator', ['user_pass' => 'password']);
        wp_set_current_user($user_id);

        $_POST = [
            'rwpsl_cleaner_nonce' => 'fake_nonce'
        ];

        add_filter('wp_redirect', function($location) use (&$captured) {
            if ($captured === null) {
                $captured = $location;
            }
            return $location;
        });

        try {
            Cleaner::handle_cleaner_request();
        } catch (\Throwable $e) {
        }

        $I->assertStringContainsString('notice=inv_req', $captured);

    }

    /**
     * 没有找到任何 meta_keys 时跳转
     */
    public function testRedirectsWhenNoMetaKeys(IntegrationTester $I): void
    {
        $user_id = $I->haveUserInDatabase('admin2', 'administrator', ['user_pass' => 'password2']);
        wp_set_current_user($user_id);

        $_POST = [
            'rwpsl_cleaner_nonce' => wp_create_nonce('rwpsl_cleaner_action'),
            'post_type' => 'post'
        ];

        add_filter('wp_redirect', function($location) use (&$captured) {
            if($captured === null){
                $captured = $location;
            }
            return $location;
        });

        try {
            Cleaner::handle_cleaner_request();
        } catch (\Throwable $e) {
        }

        $I->assertStringContainsString('cleaned=1', $captured);
    }

    /**
     * @throws \Exception
     */
    public function testExpiredTodayAndTotalAreDeleted(IntegrationTester $I): void
    {
        // 1. 创建 post
        $postId = $I->havePostInDatabase(['post_type' => 'post']);

        // 2. 插入过期 today meta
        $I->havePostmetaInDatabase($postId, '_rwpsl_today_20250805', 5);

        // 3. 插入 total meta
        $I->havePostmetaInDatabase($postId, '_rwpsl_total', 5);

        // 4. 执行清理逻辑
        try {
            Cleaner::handle_cleaner_request();
        } catch (\Throwable $e) {
        }

        // 5. 断言过期 today meta 已被删除
        $I->dontSeeInDatabase('wp_postmeta', [
            'post_id'  => $postId,
            'meta_key' => '_rwpsl_today_20250805',
        ], "断言失败：过期 today meta 仍然存在");

        // 6. 断言 total meta 已被删除
        $I->dontSeeInDatabase('wp_postmeta', [
            'post_id'  => $postId,
            'meta_key' => '_rwpsl_total',
        ], "断言失败：total meta 仍然存在");
    }

    public function testExpiredTodayDeletedButValidTodayAndTotalRemain(IntegrationTester $I): void
    {
        $postId = $I->havePostInDatabase(['post_type' => 'post']);

        // 插入过期 today
        $I->havePostmetaInDatabase($postId, '_rwpsl_today_20250805', 5);

        // 插入未过期 today
        $I->havePostmetaInDatabase($postId, '_rwpsl_today_20250914', 10);

        // 插入 total
        $I->havePostmetaInDatabase($postId, '_rwpsl_total', 15);

        try {
            Cleaner::handle_cleaner_request();
        } catch (\Throwable $e) {
        }

        // 过期 today 应该被删除
        $I->dontSeeInDatabase('wp_postmeta', [
            'post_id'  => $postId,
            'meta_key' => '_rwpsl_today_20250805',
        ], "断言失败：过期 today meta 仍然存在");

        // 未过期 today 应该保留
        $I->seeInDatabase('wp_postmeta', [
            'post_id'  => $postId,
            'meta_key' => '_rwpsl_today_20250914',
        ], "断言失败：未过期 today meta 被误删");

        // total 应该保留
        $I->seeInDatabase('wp_postmeta', [
            'post_id'  => $postId,
            'meta_key' => '_rwpsl_total',
        ], "断言失败：total meta 被误删");
    }

    public function testOnlyTodayNoTotalA(IntegrationTester $I): void
    {
        $postId = $I->havePostInDatabase(['post_type' => 'post']);

        // 只有过期 today
        $I->havePostmetaInDatabase($postId, '_rwpsl_today_20250805', 5);

        try {
            Cleaner::handle_cleaner_request();
        } catch (\Throwable $e) {
        }

        // today 应该被删除
        $I->dontSeeInDatabase('wp_postmeta', [
            'post_id'  => $postId,
            'meta_key' => '_rwpsl_today_20250805',
        ], "断言失败：过期 today meta 仍然存在");

        // total 不存在 → 不需要断言，但可以确认不会报错
    }

    public function testOnlyTodayNoTotalB(IntegrationTester $I): void
    {
        $postId = $I->havePostInDatabase(['post_type' => 'post']);

        // 只有过期 today
        $I->havePostmetaInDatabase($postId, '_rwpsl_today_20250805', 5);

        try {
            Cleaner::handle_cleaner_request();
        } catch (\Throwable $e) {
        }

        // today 应该被删除
        $I->dontSeeInDatabase('wp_postmeta', [
            'post_id'  => $postId,
            'meta_key' => '_rwpsl_today_20250805',
        ], "断言失败：过期 today meta 仍然存在");

        // total 不存在 → 不需要断言，但可以确认不会报错
    }

    /**
     * 有未过期 meta_keys 更新 total
     */
    public function testValidMetaKeysUpdateTotal(IntegrationTester $I): void
    {
        $user_id = $I->haveUserInDatabase('admin4', 'administrator', ['user_pass' => 'password']);
        wp_set_current_user($user_id);

        $post_id = $I->havePostInDatabase(['post_type' => 'post']);

        $valid_key = Tracker::RWPSL_META_KEY_TODAY_PREFIX . gmdate('Ymd');
        update_post_meta($post_id, $valid_key, 5);
        update_post_meta($post_id, Tracker::RWPSL_META_KEY_TOTAL, 0);

        $_POST = [
            'rwpsl_cleaner_nonce' => wp_create_nonce('rwpsl_cleaner_action'),
            'post_type' => 'post'
        ];

        try {
            Cleaner::handle_cleaner_request();
        } catch (\Throwable $e) {
        }

        $I->seePostMetaInDatabase([
            'post_id' => $post_id,
            'meta_key' => Tracker::RWPSL_META_KEY_TOTAL,
            'meta_value' => '5'
        ]);
    }

    /**
     * post_type 非法时回退到 post
     */
    public function testInvalidPostTypeFallsBackToPost(IntegrationTester $I): void
    {
        $user_id = $I->haveUserInDatabase('admin5', 'administrator', ['user_pass' => 'password']);
        wp_set_current_user($user_id);

        $post_id = $I->havePostInDatabase(['post_type' => 'post']);

        $valid_key = Tracker::RWPSL_META_KEY_TODAY_PREFIX . gmdate('Ymd');
        update_post_meta($post_id, $valid_key, 3);
        update_post_meta($post_id, Tracker::RWPSL_META_KEY_TOTAL, 0);

        $_POST = [
            'rwpsl_cleaner_nonce' => wp_create_nonce('rwpsl_cleaner_action'),
            'post_type' => 'invalid_type'
        ];

        try {
            Cleaner::handle_cleaner_request();
        } catch (\Throwable $e) {
        }

        $I->seePostMetaInDatabase([
            'post_id' => $post_id,
            'meta_key' => Tracker::RWPSL_META_KEY_TOTAL,
            'meta_value' => '3'
        ]);
    }
}

