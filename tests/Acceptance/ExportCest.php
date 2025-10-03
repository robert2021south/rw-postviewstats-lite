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

        $I->loginAsAdmin(); // 假设有封装好的登录方法

    }

    //测试页面是否渲染成功
    /*
    public function exportPageLoads(AcceptanceTester $I)
    {
        $I->amOnPage('/wp-admin/admin.php?page=rwpsl-export');

        // 页面标题或主元素可见
        $I->see('Data Export', 'h1');

        // 验证表单元素
        $I->seeElement('form#export-form');
        $I->seeElement('select[name=post_type]');
        $I->seeElement('input[name=rwpsl_export_nonce]');

        // 验证导出按钮存在
        $I->seeElement('#submit');

        // 验证升级按钮或链接存在
        $I->seeElement('a[href*="upgrade"]');
    }*/


    //测试主导出路径
    public function exportPostsCsv(AcceptanceTester $I): void
    {
        // 1. 登录后台
        //$I->loginAsAdmin();


        // 2. 打开导出页面
        $I->amOnAdminPage('admin.php?page=rwpsl-export');
        //$I->see('数据导出'); // 页面标题断言

        // 3. 选择文章类型（比如文章 post）
        $postType = 'post'; // 你在测试里决定选择哪个 post_type
        $I->selectOption('select[name=post_type]', $postType);

        $I->waitForElementVisible('#submit', 5);

        // 4. 点击导出按钮
        $I->click('#submit'); // 用 id

        //等待处理并下载
        sleep(5); // 等待浏览器接收请求并开始下载

        // 5. 等待文件下载（需要 WebDriver 配置 downloadPath）
        // 下载目录
        $downloadDir = codecept_root_dir() . 'tests/_output/';

        //$downloadPath = codecept_output_dir() . 'page-views-export-'.$postType.'.csv';

        // 找到最新的文件
        $files = glob($downloadDir . "page-views-export-{$postType}-*.csv");
        sort($files);
        $latestFile = end($files);

        // 断言文件存在
        $I->assertFileExists($latestFile);

        // 可选
        // 6. 验证 CSV 文件内容
        //$csvContent = file_get_contents($downloadPath);
        $csvContent = file($latestFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        // 去掉第一行的 BOM
        $firstLine = preg_replace('/^\xEF\xBB\xBF/', '', $csvContent[0]);
        $header = str_getcsv($firstLine);
        //Assert::assertEquals(['Post ID', 'Title', 'Views'], $header);
        $I->assertEquals(['Post ID', 'Title', 'Views'],$header);

        //$csvContent = preg_replace('/^\xEF\xBB\xBF/', '', $csvContent);
        //Assert::assertStringContainsString('Post ID,Title,Views', $csvContent);

        //$header = str_getcsv(trim($csvContent[0], "\xEF\xBB\xBF")); // 去掉 BOM 再解析
        //Assert::assertEquals(['Post ID', 'Title', 'Views'], $header);

        // 假设测试文章标题为 "Hello world!"
        //Assert::assertStringContainsString('Hello world!', $csvContent);

    }

    /*
    public function exportLiteRestrictedType(AcceptanceTester $I): void
    {
        $restrictedType = 'product'; // Lite 不支持的 post_type
        // 1. 登录后台
        $I->loginAsAdmin();

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
        $postType = 'page'; // 假设没有页面

        // 1. 登录后台
        //$I->loginAsAdmin();

        // 打开导出页面
        $I->amOnPage('/wp-admin/admin.php?page=rwpsl-export');

        // 选择 page
        $I->selectOption('select[name=post_type]', $postType);

        //$I->waitForElementVisible('#submit', 5);
        // 点击提交
        $I->click('#submit');

        // 验证 URL 上包含提示
        $I->seeInCurrentUrl('notice=no_posts');

        // 验证页面上有提示信息（根据你页面实际渲染的 DOM 调整选择器）
        //$I->see('No posts available for export');

        // 验证没有下载 CSV 文件
        $I->dontSeeFileFound(codecept_output_dir() . "page-views-export-{$postType}*.csv");
    }


}