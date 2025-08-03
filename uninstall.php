<?php


if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

use RobertWP\PostViewStatsLite\Core\Bootstrap;

Bootstrap::uninstall();
