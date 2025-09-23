<?php

namespace Tests\Integration;

use RobertWP\PostViewStatsLite\Admin\Settings\SettingsRegistrar;
use RobertWP\PostViewStatsLite\Modules\Tracker\Tracker;
use Tests\Support\IntegrationTester;

class TrackerCest
{
    function _before(IntegrationTester $I): void
    {
    }

    public function testGetViews(IntegrationTester $I): void
    {
        $I->wantTo('Test Tracker::get_views in a real WordPress environment');

        // 创建测试文章
        $post_id = wp_insert_post([
            'post_title'  => 'Functional Test Post',
            'post_status' => 'publish'
        ]);

        // 设置浏览量 meta
        update_post_meta($post_id, '_rwpsl_total', 7);

        // 调用方法
        $views = Tracker::get_views($post_id);

        // 验证返回值
        $I->assertEquals(7, $views, 'get_views 应该返回 meta 中设置的值');

        // 清理测试文章
        wp_delete_post($post_id, true);
    }

    /**
     * 测试 track_views() 是否正确更新总浏览量和今日浏览量
     */
    public function testTrackViewsUpdatesMeta(IntegrationTester $I): void
    {
        $I->wantTo('Test Tracker::track_views updates total and today views correctly');

        // 创建测试文章
        $post_id = wp_insert_post([
            'post_title'  => 'Functional Test Post for track_views',
            'post_status' => 'publish'
        ]);

        // 确保初始浏览量为 0
        $I->assertEquals(0, (int) get_post_meta($post_id, Tracker::RWPSL_META_KEY_TOTAL, true));
        $today_key = Tracker::RWPSL_META_KEY_TODAY_PREFIX . gmdate('Ymd');
        $I->assertEquals(0, (int) get_post_meta($post_id, $today_key, true));

        //前置条件
        $data = [];
        $data['stat_enabled'] = 1;
        $data['sort_enabled'] = 1;
        $data['rest_api_enabled'] = 1;
        $data['delete_data_on_uninstall'] = 0;
        update_option( SettingsRegistrar::OPTION_SITE_SETTINGS, $data );

        // 调用 track_views 方法
        $tracker = new Tracker();
        $tracker->track_views($post_id);

        // 验证总浏览量 +1
        $I->assertEquals(1, (int) get_post_meta($post_id, Tracker::RWPSL_META_KEY_TOTAL, true));

        // 验证今日浏览量 +1
        $I->assertEquals(1, (int) get_post_meta($post_id, $today_key, true));

        // 再调用一次，验证累计效果
        $tracker->track_views($post_id);
        $I->assertEquals(2, (int) get_post_meta($post_id, Tracker::RWPSL_META_KEY_TOTAL, true));
        $I->assertEquals(2, (int) get_post_meta($post_id, $today_key, true));

        // 清理测试文章
        wp_delete_post($post_id, true);
    }

}
