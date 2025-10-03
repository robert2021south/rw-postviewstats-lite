<?php

declare(strict_types=1);

namespace Tests\Support\Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

use Codeception\Exception\ModuleConfigException;
use Codeception\Exception\ModuleException;

class Acceptance extends \Codeception\Module
{

    public function waitForFileDownload(string $postType, int $timeout = 15): string
    {
        $downloadsDir = 'E:\\Downloads'; // 实际 Chrome 下载目录
        $destDir = rtrim(codecept_output_dir(), '\\/') . '\\';
        $prefix = "page-views-export-{$postType}-";
        $elapsed = 0;

        while ($elapsed < $timeout) {
            $files = glob($downloadsDir . '\\' . $prefix . '*.csv');
            if ($files) {
                // 找最新的文件
                usort($files, fn($a, $b) => filemtime($b) <=> filemtime($a));
                $latest = $files[0];
                $dest = $destDir . basename($latest);
                copy($latest, $dest);
                return $dest;
            }
            sleep(1);
            $elapsed++;
        }

        throw new \Exception("No file with prefix '$prefix' downloaded in $downloadsDir");
    }

    /**
     * @throws ModuleConfigException
     * @throws ModuleException
     */
    public function _beforeSuite($settings = []): void
    {
        $wp = $this->getModule('WPWebDriver');

        if (getenv('WP_ADMIN_USER') && getenv('WP_ADMIN_PASS')) {
            $wp->_setConfig([
                'adminUsername' => getenv('WP_ADMIN_USER'),
                'adminPassword' => getenv('WP_ADMIN_PASS'),
            ]);
        }
    }
}
