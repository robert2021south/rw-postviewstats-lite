<?php
namespace Tests\Acceptance;

use Facebook\WebDriver\Remote\RemoteWebDriver;
use PHPUnit\Framework\Assert;
use Tests\Support\AcceptanceTester;

class ExportCest{

    public function _before(AcceptanceTester $I): void
    {
        $I->executeInSelenium(function(RemoteWebDriver $webDriver) {
            $webDriver->manage()->deleteAllCookies();
        });

        // 1. 登录后台
        $I->loginAsAdmin();
    }

    //测试主导出路径
    public function exportPostsCsv(AcceptanceTester $I): void
    {

        // 2. 打开导出页面
        $I->amOnAdminPage('admin.php?page=rwpsl-export');

        // 3. 选择文章类型
        $postType = 'post';
        $I->waitForElement('select[name=post_type]', 10); // 最多等待 10 秒
        $I->selectOption('select[name=post_type]', $postType);

        $I->waitForElementVisible('#submit', 5);


        // 4. 点击导出按钮
        $I->click('#submit');

        // 5. 等待文件生成
        $I->wait(3); // 等待下载完成

        // 6. 配置 Chrome 下载目录
        // WPBrowser 默认下载路径
        $downloadDir = codecept_output_dir(); // 已映射到容器和宿主机

        // 7. 获取最新下载的 CSV 文件
        $files = glob($downloadDir . "page-views-export-{$postType}-*.csv");
        $I->assertNotEmpty($files, "No CSV file found in $downloadDir");

        sort($files);
        $latestFile = end($files);
        $I->assertFileExists($latestFile);

        // 8. 读取 CSV 内容
        $csvContent = file($latestFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $csvContent[0] = preg_replace('/^\xEF\xBB\xBF/', '', $csvContent[0]);
        $header = str_getcsv($csvContent[0]);

        // 断言表头正确
        $I->assertEquals(['Post ID', 'Title', 'Views'], $header);

        // 可选：断言 CSV 包含测试文章
        $found = false;
        foreach (array_slice($csvContent, 1) as $line) {
            $cols = str_getcsv($line);
            if (isset($cols[1]) && $cols[1] === 'Hello world!') {
                $found = true;
                break;
            }
        }
        $I->assertTrue($found, 'CSV does not contain the test post.');
    }


    /*
    public function exportLiteRestrictedType(AcceptanceTester $I): void
    {
        $restrictedType = 'product'; // Lite 不支持的 post_type

        // 打开导出页面
        $I->amOnPage('/wp-admin/admin.php?page=rwpsl-export');

        // 选择一个不允许的 post_type
        $I->selectOption('select[name=post_type]', $restrictedType);

        //$I->waitForElementVisible('#submit', 5);
        // 点击提交
        $I->click('#submit');

        // 验证 URL 上包含提示
        $I->seeInCurrentUrl('notice=pro_only');

        // 验证页面上有提示信息（根据你页面实际渲染的 DOM 调整选择器）
        //$I->see('This feature is only available in Pro version');

        // 验证没有下载 CSV 文件
        $I->dontSeeFileFound(codecept_output_dir() . "page-views-export-{$restrictedType}*.csv");
    }
    */

    public function exportNoPosts(AcceptanceTester $I): void
    {
        $postType = 'page'; // 随便写一个不存在的post_type

        global $wpdb;
        $wpdb->query( "DELETE FROM {$wpdb->posts} WHERE post_type = 'page'" );

        // 2. 打开导出页面
        $I->amOnPage('/wp-admin/admin.php?page=rwpsl-export');

        // 3. 选择 page
        $I->waitForElement('select[name=post_type]', 10); // 最多等待 10 秒
        $I->selectOption('select[name=post_type]', $postType);

        //$I->waitForElementVisible('#submit', 5);

        // 4. 点击提交
        $I->click('#submit');

        // 5. 等待文件生成
        $I->wait(3); // 等待下载完成

        // 6. 验证 URL 上包含提示
        $I->seeInCurrentUrl('notice=no_posts');

    }


}