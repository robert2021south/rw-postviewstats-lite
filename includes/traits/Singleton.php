<?php
namespace RobertWP\PostViewStatsLite\Traits;

if (!defined('ABSPATH')) exit;

use Exception;

trait Singleton {
    private static $instance = null;

    public static function get_instance() {
        if ( null === static::$instance ) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    /**
     * Prevent from being clone
     * @throws Exception
     */
    public function __clone() {throw new Exception("Cloning is not allowed for " . __CLASS__);}

    /**
     * Prevent deserialization from generating multiple instances.
     * @throws Exception
     */
    public function __wakeup() {throw new Exception("Cannot unserialize instance of " . __CLASS__);}
}
