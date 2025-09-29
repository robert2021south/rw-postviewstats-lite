<?php
namespace RobertWP\PostViewStatsLite\Core;

class CallbackWrapper {
    private static array $callbackCache = [];

    public static function plugin_context_only(callable $callback): callable {
        $key = self::generate_callback_key($callback);

        // 如果已缓存，则返回
        if (isset(self::$callbackCache[$key])) {
            return self::$callbackCache[$key];
        }

        // 否则封装并缓存
        self::$callbackCache[$key] = function (...$args) use ($callback) {
            if (!Context::is_plugin_context()) {
                return is_array($args[0] ?? null) ? $args[0] : [];
            }
            return call_user_func_array($callback, $args);
        };

        return self::$callbackCache[$key];
    }

    private static function generate_callback_key(callable $callback): string {
        if (is_array($callback)) {
            $object = is_object($callback[0]) ? spl_object_hash($callback[0]) : $callback[0];
            return $object . '::' . $callback[1];
        }

        if (is_string($callback)) {
            return $callback;
        }

        return spl_object_hash((object)$callback);
    }
}

