<?php
namespace RobertWP\PostViewStatsLite\Modules\Export;

use RobertWP\PostViewStatsLite\Modules\Tracker\Tracker;
use RobertWP\PostViewStatsLite\Traits\Singleton;
use RobertWP\PostViewStatsLite\Utils\Helper;
use RobertWP\PostViewStatsLite\Utils\TemplateLoader;
use function RobertWP\PostViewStatsLite\Utils\rwpsl_wp_die;

class PostViewsExporter {
    use Singleton;

    public static function add_export_submenu(): void
    {
        add_submenu_page(
            'rwpsl-settings', // 顶级菜单的 slug
            __('Data Export', 'rw-postviewstats-lite'),
            __('Data Export', 'rw-postviewstats-lite'),
            'manage_options',
            'rwpsl-export',
            [self::class, 'render_export_page']
        );
    }

    public static function render_export_page(): void
    {
        // 准备模板需要的变量
        $template_args = [
            'admin_post_url' => admin_url('admin-post.php'),
            'nonce_action' => 'rwpsl_export_csv',
            'post_types' => get_post_types(['public' => true], 'objects'),
            'upgrade_url' => Helper::get_upgrade_url('export')
        ];

        // 加载模板
        TemplateLoader::load('export-page', $template_args, 'export');
    }

    public static function handle_export_csv(): void
    {

        if (!current_user_can( 'manage_options')) {
            wp_redirect(admin_url('admin.php?page=rwpsl-export&notice=ins_perm'));
            rwpsl_wp_die();
        }

        $nonce = sanitize_text_field( wp_unslash( $_POST['rwpsl_export_nonce'] ?? '' ) );
        if (empty( $_POST['rwpsl_export_nonce'] ) ||!wp_verify_nonce( $nonce, 'rwpsl_export_csv' )
        ) {
            wp_redirect(admin_url('admin.php?page=rwpsl-export&notice=sec_chk_fail'));
            rwpsl_wp_die();
        }

        $post_type = isset( $_POST['post_type'] ) ? sanitize_text_field( wp_unslash($_POST['post_type']) ) : 'post';

        $posts = get_posts( [
            'numberposts' => -1,
            'post_type'   => $post_type,
            'post_status' => 'publish',
        ] );

        if (! in_array($post_type, ['post', 'page'], true)) {
            wp_redirect(admin_url('admin.php?page=rwpsl-export&notice=pro_only'));
            rwpsl_wp_die();
        }

        if ( empty( $posts ) ) {
            wp_redirect(admin_url('admin.php?page=rwpsl-export&notice=no_posts'));
            rwpsl_wp_die();
        }

        $filename = "page-views-export-{$post_type}-" . gmdate( 'Y-m-d_H-i-s' ) . ".csv";

        header( 'Content-Type: text/csv; charset=utf-8' );
        header( "Content-Disposition: attachment; filename={$filename}" );

        // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_fopen
        $output = fopen( 'php://output', 'w' );

        // Output UTF-8 BOM header to ensure Excel recognizes encoding.
        // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_write_fwrite
        fwrite( $output, chr( 0xEF ) . chr( 0xBB ) . chr( 0xBF ) );

        fputcsv( $output, [ 'Post ID', 'Title', 'Views' ] );

        foreach ( $posts as $post ) {
            $views = Tracker::get_views( $post->ID );
            fputcsv( $output, [ $post->ID, $post->post_title, $views ] );
        }

        // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_fclose
        fclose( $output );
        rwpsl_wp_die();
    }

}
