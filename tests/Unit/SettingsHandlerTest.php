<?php
namespace RobertWP\PostViewStatsLite\Admin\Settings;

// Mock WordPress functions

use Tests\Unit\SettingsHandlerTest;

if (!function_exists('current_user_can')) {
    function current_user_can($capability): bool
    {
        return SettingsHandlerTest::$current_user_can ?? true;
    }
}

if (!function_exists('wp_verify_nonce')) {
    function wp_verify_nonce($nonce, $action): bool
    {
        return SettingsHandlerTest::$wp_verify_nonce ?? true;
    }
}

if (!function_exists('update_option')) {
    function update_option($option_name, $value): bool
    {
        SettingsHandlerTest::$updated_option = ['name' => $option_name, 'value' => $value];
        return true;
    }
}

if (!function_exists('wp_redirect')) {
    function wp_redirect($url)
    {
        SettingsHandlerTest::$redirect_url = $url;
        throw new \Exception("Redirected to $url");
    }
}

if (!function_exists('admin_url')) {
    function admin_url($path): string
    {
        return 'admin_url/' . $path;
    }
}

if (!function_exists('network_admin_url')) {
    function network_admin_url($path): string
    {
        return 'network_admin_url/' . $path;
    }
}

if (!function_exists('sanitize_text_field')) {
    function sanitize_text_field($input): string
    {
        return trim($input);
    }
}

if (!function_exists('wp_unslash')) {
    function wp_unslash($value)
    {
        return $value;
    }
}

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use RobertWP\PostViewStatsLite\Admin\Settings\SettingsHandler;
use RobertWP\PostViewStatsLite\Admin\Settings\SettingsRegistrar;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class SettingsHandlerTest extends TestCase
{

    protected SettingsHandler $settingsHandler;

    public static $current_user_can;
    public static $wp_verify_nonce;
    public static $updated_option;
    public static $redirect_url;

    protected function setUp(): void
    {
        parent::setUp();
        $this->settingsHandler = new SettingsHandler();
        $_POST = [];
        $_SERVER = [];
        self::$current_user_can = null;
        self::$wp_verify_nonce = null;
        self::$updated_option = null;
        self::$redirect_url = null;
    }

    protected function tearDown(): void
    {
        $_POST = [];
        $_SERVER = [];
        self::$current_user_can = null;
        self::$wp_verify_nonce = null;
        self::$updated_option = null;
        self::$redirect_url = null;
    }

    public function test_non_post_request_returns()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        // 方法不返回值，只要不抛异常就通过
        $this->settingsHandler->handle_settings_form();

        // 可选：断言 wp_redirect 没被调用
        $this->assertNull(self::$redirect_url);
    }

    public function test_permission_denied_redirects()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        self::$current_user_can = false;

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Redirected to admin_url/admin.php?page=rwpsl-settings&notice=ins_perm');

        $this->settingsHandler->handle_settings_form();
    }

    public function test_invalid_nonce_redirects()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        self::$current_user_can = true;
        $_POST['_wpnonce'] = 'fake';
        self::$wp_verify_nonce = false;

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Redirected to network_admin_url/admin.php?page=rwpsl-settings&notice=inv_nonce');

        $this->settingsHandler->handle_settings_form();
    }

    public function test_valid_post_saves_settings_and_redirects()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        self::$current_user_can = true;
        $_POST['_wpnonce'] = 'valid';
        self::$wp_verify_nonce = true;

        $_POST['stat_enabled'] = '1';
        $_POST['sort_enabled'] = '';
        $_POST['rest_api_enabled'] = '1';
        $_POST['delete_data_on_uninstall'] = '';

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Redirected to admin_url/admin.php?page=rwpsl-settings&notice=success&context=settings');

        $this->settingsHandler->handle_settings_form();

        $this->assertNotNull(self::$updated_option);
        $this->assertEquals(SettingsRegistrar::OPTION_SITE_SETTINGS, self::$updated_option['name']);
        $this->assertEquals([
            'stat_enabled' => 1,
            'sort_enabled' => 0,
            'rest_api_enabled' => 1,
            'delete_data_on_uninstall' => 0
        ], self::$updated_option['value']);
    }
}
