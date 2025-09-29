<?php
namespace Tests\Integration;

use RobertWP\PostViewStatsLite\Modules\Export\PostViewsExporter;
use Tests\Support\IntegrationTester;

class ExportCest{

    public function _before(IntegrationTester $I): void
    {
        wp_cache_flush();
    }

    public function _after(IntegrationTester $I): void
    {
        wp_cache_flush();
    }

    /**
     * 用例 1: 无权限用户 → ins_perm
     */
    public function testNoPermissionUser(IntegrationTester $I): void
    {
        // 模拟一个没有 manage_options 权限的用户
        $userId = $I->haveUserInDatabase(
            'subscriber',               // 用户登录名
            'subscriber',                   // 角色也可以直接写字符串
            [ 'user_pass' => 'password' ]       // 额外字段
        );
        // 切换当前用户
        wp_set_current_user($userId);


        $captured = null;
        add_filter('wp_redirect', function ($location) use (&$captured) {
            $captured = $location;
            return $location;
        });

        try {
            PostViewsExporter::handle_export_csv();
        } catch (\Exception $e) {
            $I->assertSame('terminate called', $e->getMessage());
        }

        $I->assertNotNull($captured);
        $I->assertStringContainsString('notice=ins_perm', $captured);
    }

    /**
     * 用例 2: 有权限 + nonce 失败 → sec_chk_fail
     */
    public function testNonceFail(IntegrationTester $I): void
    {
        $userId = $I->haveUserInDatabase('adminuser', 'administrator', ['user_pass' => '123456']);
        wp_set_current_user($userId);

        $captured = null;
        add_filter('wp_redirect', function ($location) use (&$captured) {
            $captured = $location;
            return $location;
        });

        $_POST['rwpsl_export_nonce'] = ''; // 无效
        $_POST['post_type'] = 'post';

        try {
            PostViewsExporter::handle_export_csv();
        } catch (\Exception $e) {
            $I->assertSame('terminate called', $e->getMessage());
        }

        $I->assertNotNull($captured);
        $I->assertStringContainsString('notice=sec_chk_fail', $captured);

    }

    /**
     * 用例 3: 有权限 + nonce 有效 + post_type 不允许 → pro_only
     */
    public function testProOnlyPostType(IntegrationTester $I): void
    {
        $userId = $I->haveUserInDatabase('adminuser', 'administrator', ['user_pass' => '123456']);
        wp_set_current_user($userId);

        $captured = null;
        add_filter('wp_redirect', function ($location) use (&$captured) {
            $captured = $location;
            return $location;
        });

        $_POST['rwpsl_export_nonce'] = wp_create_nonce('rwpsl_export_csv');
        $_POST['post_type'] = 'product';

        try {
            PostViewsExporter::handle_export_csv();
        } catch (\Exception $e) {
            $I->assertSame('terminate called', $e->getMessage());
        }

        $I->assertNotNull($captured);
        $I->assertStringContainsString('notice=pro_only', $captured);

    }

    /**
     * 用例 4: 有权限 + nonce 有效 + post_type=post + 无文章 → no_posts
     */
    public function testNoPosts(IntegrationTester $I): void
    {
        $userId = $I->haveUserInDatabase('adminuser', 'administrator', ['user_pass' => '123456']);
        wp_set_current_user($userId);

        global $wpdb;
        $wpdb->query("DELETE FROM {$wpdb->posts} WHERE post_type='post'"); // 清空 posts

        $captured = null;
        add_filter('wp_redirect', function ($location) use (&$captured) {
            $captured = $location;
            return $location;
        });

        $_POST['rwpsl_export_nonce'] = wp_create_nonce('rwpsl_export_csv');
        $_POST['post_type'] = 'post';

        try {
            PostViewsExporter::handle_export_csv();
        } catch (\Exception $e) {
            $I->assertSame('terminate called', $e->getMessage());
        }

        $I->assertNotNull($captured);
        $I->assertStringContainsString('notice=no_posts', $captured);
    }

    /**
     * 用例 5: 有权限 + nonce 有效 + post_type=post + 有文章 → 导出 CSV （happy path）
     */
    public function testExportCsv(IntegrationTester $I): void
    {
        $userId = $I->haveUserInDatabase('adminuser', 'administrator', ['user_pass' => '123456']);
        wp_set_current_user($userId);

        // 插入一篇文章
        $I->havePostInDatabase([
            'post_title' => 'Test Post',
            'post_status' => 'publish',
            'post_type' => 'post',
        ]);

        $_POST['rwpsl_export_nonce'] = wp_create_nonce('rwpsl_export_csv');
        $_POST['post_type'] = 'post';

        // 捕获 CSV 输出
        ob_start();
        try {
            PostViewsExporter::handle_export_csv();
        } catch (\Exception $e) {
            $I->assertSame('terminate called', $e->getMessage());
        }
        $output = ob_get_clean();

        // 去掉 BOM 断言
        $I->assertStringContainsString('"Post ID",Title,Views', $output);
        $I->assertStringContainsString('"Test Post"', $output);

    }
}
