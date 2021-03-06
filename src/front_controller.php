<?php

// Use this to implement a minimal front controller to use instead
// of WordPress' index.php
//
// it works like this: rename WordPress' index.php to something
// else, e.g. wp-index.php. create a new index.php that looks about
// so:
//
// ```php
// <?php
//
// require_once __DIR__ . '/wp-content/themes/testtheme/vendor/autoload.php';
//
// Em4nl\Unplug\front_controller(__DIR__ . '/wp-index.php');
// ```
//
// Also, you'll need an additional unplug-config.php file in your
// WordPress root dir with these two definitions in it:
//
// ```php
// <?php
//
// define('UNPLUG_CACHE_DIR', __DIR__ . '/_unplug_cache');
// define('UNPLUG_CACHE_ON', TRUE);
// ```
//
// Note that this bypasses WordPress completely
//
// (Instead of renaming index.php, you might also e.g. change the
// default redirect rule in your .htaccess)
//


namespace Em4nl\Unplug;


if (!function_exists('Em4nl\Unplug\front_controller')) {
    function front_controller($wp_index_php, $invalidate=NULL) {
        define('UNPLUG_FRONT_CONTROLLER', TRUE);
        if (UNPLUG_CACHE_ON) {
            global $_unplug_cache;
            $_unplug_cache = new \Em4nl\U\Cache(UNPLUG_CACHE_DIR);
            if ($invalidate) {
                $_unplug_cache->invalidate($invalidate);
            }
            $served_from_cache = $_unplug_cache->serve();
            if (!$served_from_cache) {
                $_unplug_cache->start();
                include_once $wp_index_php;
                $_unplug_cache->end(!defined('UNPLUG_DO_CACHE') || UNPLUG_DO_CACHE);
            }
        } else {
            include_once $wp_index_php;
        }
    }
}
