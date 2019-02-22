<?php

namespace Em4nl\Unplug;


class Cache {

    function __construct($dir) {

        // create the cache dir if it doesn't exist
        if (!file_exists($dir)) {
            mkdir($dir, 0755);
        }

        $this->dir = realpath($dir);
        self::assert_sha256_available();
    }

    function start() {
        // TODO start buffering
    }

    function end() {
        // TODO end buffering, save response if headers are good
    }

    function serve($path) {
        // TODO try to serve cached file and return TRUE, otherwise
        // (if file doesn't exist) return FALSE
        return FALSE;
    }

    function flush() {
        // delete everything in the cache dir,
        // but not the cache dir itself.
        // TODO make empty_cache_directory thread safe
        // TODO again: why does this have to be thread safe?
        // $this->empty_cache_directory();
    }

    function add($path, $response) {

        // TODO
        // - get filename extension from headers
        // - if that fails or is unclear, get extension from path
        // - or maybe the other way round: get extension from path,
        //   and if it has no extension, use headers. maybe better.

        if ($response instanceof ContentResponse) {
            $filename = $this->save($path, $response);
        }
    }

    function save($path, $response) {

        $hash = hash('sha256', $path);

        $filename = $hash . '.' . $response->get_extension();
        $file = $this->dir . '/' . $filename;

        $success = file_put_contents($file, $response->get_body());

        if ($success === false) {
            throw new \Exception('Failed to write ' . $file);
        }

        return $filename;
    }

    function empty_cache_directory() {

        self::empty_directory($this->dir);
    }

    static function assert_sha256_available() {

        if (!in_array('sha256', hash_algos(), true)) {
            throw new \Exception('SHA256 is not available');
        }
    }

    static function recursive_remove_directory($directory) {

        self::empty_directory($directory);

        rmdir($directory);
    }

    static function empty_directory($directory) {

        foreach (glob("{$directory}/*") as $file) {

            if (is_dir($file)) {
                self::recursive_remove_directory($file);
            } else {
                unlink($file);
            }
        }
    }
}
