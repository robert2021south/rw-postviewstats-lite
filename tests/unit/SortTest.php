<?php
namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use RobertWP\PostViewStatsLite\Modules\Sort\Sort;

class SortTest extends TestCase
{

    public function testMakeViewsColumnSortableAddsPostViews()
    {
        // 原始 columns 数组
        $columns = [
            'title' => 'Title',
            'date' => 'Date'
        ];

        // 调用静态方法
        $result = Sort::make_views_column_sortable($columns);

        // 断言 'post_views' 已被添加
        $this->assertArrayHasKey('post_views', $result);
        $this->assertEquals('views', $result['post_views']);

        // 断言原始列仍然存在
        $this->assertArrayHasKey('title', $result);
        $this->assertArrayHasKey('date', $result);
    }

    public function testMakeViewsColumnSortableDoesNotRemoveExistingColumns()
    {
        $columns = [
            'title' => 'Title',
            'author' => 'Author'
        ];

        $result = Sort::make_views_column_sortable($columns);

        // 确保原来的列没有被覆盖
        $this->assertEquals('Title', $result['title']);
        $this->assertEquals('Author', $result['author']);
    }


}
