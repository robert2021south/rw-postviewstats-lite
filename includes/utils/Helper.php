<?php
namespace RobertWP\PostViewStatsLite\Utils;

class Helper
{


    public static function get_global_option($key, $default = false)
    {
        return is_multisite() ? get_site_option($key, $default) : get_option($key, $default);
    }

    public static function update_global_option($key, $value)
    {
        return is_multisite() ? update_site_option($key, $value) : update_option($key, $value);
    }

    public static function delete_global_option($key)
    {
        return is_multisite() ? delete_site_option($key) : delete_option($key);
    }

    public static function generate_tracking_id($purchase_code) {
        return md5($purchase_code . home_url());
    }

    public static function generate_nonce() {
        try {
            $nonce = bin2hex(random_bytes(16));
        } catch (\Exception $e) {
            $nonce = wp_generate_uuid4();
        }
        return $nonce;
    }

    public static function is_valid_purchase_code($code) {
        if($code == 'VALID-MOCK-CODE') return true;
        return preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $code) === 1;
    }

    /**
     * 获取管理员邮箱，优先获取网站管理员邮箱
     *
     * @return string
     */
    public static function get_admin_email()
    {
        $admin_email = get_bloginfo('admin_email');
        return $admin_email ?: '';
    }

    public static function get_valid_ip()
    {
        if (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = trim(sanitize_text_field(wp_unslash( $_SERVER['REMOTE_ADDR'] ?? '')));

            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6)) {
                return $ip;
            }
        }

        $trusted_proxy_headers = [
            'HTTP_CF_CONNECTING_IP', // Cloudflare
            'HTTP_X_REAL_IP',        // Nginx
            //'HTTP_X_FORWARDED_FOR'
        ];

        foreach ($trusted_proxy_headers as $key) {
            if (isset($_SERVER[$key])) {
                $ip = trim(sanitize_text_field( wp_unslash( $_SERVER[$key] ?? '')));
                foreach (explode(',', $ip) as $ip_part) {
                    $ip_part = trim($ip_part);
                    if (filter_var($ip_part, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6)) {
                        return $ip_part;
                    }
                }
            }
        }

        return 'unknown';
    }

    public static function get_upgrade_url( $source = ''): string {

        $base_url = 'https://robertwp.com/rw-postviewstats-pro/';
        if ( $source ) {
            return add_query_arg( 'source', $source, $base_url );
        }
        return $base_url;

        //return esc_url('https://codecanyon.net/item/rw-postviewstats-pro/88888');

    }

    /**
     * 安全写入插件日志
     *
     * @param mixed  $data      要记录的内容，可以是字符串、数组或对象
     * @param string $context   可选，日志上下文前缀，默认为插件名
     *
     */
    /*
     * 使用示例：
     * 简单字符串
        myplugin_log( 'This is a debug message' );
       数组/对象
        myplugin_log( [
            'post_id' => 123,
            'status'  => 'failed'
        ] );
       自定义上下文
        myplugin_log( 'Something happened', 'MyPluginImport' );
     * */
    public static function rw_error_log( $data, $context = 'rwpsl' ) {
        // 只在调试模式下记录日志
        if ( ! defined( 'WP_DEBUG' ) || ! WP_DEBUG ) {
            return;
        }

        // 如果是数组或对象，转换成可读字符串
        if ( is_array( $data ) || is_object( $data ) ) {
            $data = var_export( $data, true );
        } else {
            $data = (string) $data;
        }

        // 添加时间戳和上下文前缀
        $message = sprintf( '[%s] [%s] %s', $context, gmdate( 'Y-m-d H:i:s' ), $data );

        // 写入 PHP 错误日志
        error_log( $message );
    }

    public static function terminate(): void {
        if (defined('WP_ENV') && WP_ENV === 'testing') {
            throw new \Exception('terminate called');
        }
        exit;
    }

}


