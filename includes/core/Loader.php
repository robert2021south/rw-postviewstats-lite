<?php
/*
 *
 * */
namespace RobertWP\PostViewStatsLite\Core;

use RobertWP\PostViewStatsLite\Modules\Cleaner\Cleaner;
use RobertWP\PostViewStatsLite\Modules\Export\PostViewsExporter;
use RobertWP\PostViewStatsLite\Modules\PostColumn\PostViewsColumn;
use RobertWP\PostViewStatsLite\Modules\RestApi\RestApi;
use RobertWP\PostViewStatsLite\Modules\Shortcode\ShortcodeHandler;
use RobertWP\PostViewStatsLite\Modules\Sort\Sort;
use RobertWP\PostViewStatsLite\Modules\Tracker\Tracker;

class Loader {

    public static function load_features() {
        new Tracker();
        new ShortcodeHandler();
        new PostViewsColumn();

        PostViewsExporter::get_instance();
        Sort::get_instance();
        Cleaner::get_instance();
        RestApi::get_instance();
    }

}
