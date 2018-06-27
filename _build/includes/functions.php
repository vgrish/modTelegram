<?php

/**
 * @param $filename
 *
 * @return string
 */

if (!function_exists('getSnippetContent')) {
    function getSnippetContent($filename)
    {
        $file = trim(file_get_contents($filename));
        preg_match('#\<\?php(.*)#is', $file, $data);

        return rtrim(rtrim(trim($data[1]), '?>'));
    }
}

/**
 * Recursive directory remove
 *
 * @param $dir
 */

if (!function_exists('rrmdir')) {
    function rrmdir($dir)
    {
        if (is_dir($dir)) {
            $objects = scandir($dir, 1);

            foreach ($objects as $object) {
                if ($object !== "." && $object !== "..") {
                    if (filetype($dir . "/" . $object) === "dir") {
                        rrmdir($dir . "/" . $object);
                    } else {
                        unlink($dir . "/" . $object);
                    }
                }
            }

            reset($objects);
            rmdir($dir);
        }
    }
}


if (!function_exists('download')) {
    function download($url, $path)
    {
        if (ini_get('allow_url_fopen')) {
            $file = @file_get_contents($url);
        } else {
            if (function_exists('curl_init')) {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_TIMEOUT, 10);
                $safeMode = @ini_get('safe_mode');
                $openBasedir = @ini_get('open_basedir');
                if (empty($safeMode) AND empty($openBasedir)) {
                    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
                }
                $file = curl_exec($ch);
                curl_close($ch);
            } else {
                return false;
            }
        }
        file_put_contents($path, $file);

        if (!is_file($path) OR !is_readable($path)) {
            return false;
        }

        return true;
    }
}


if (!function_exists("function_enabled")) {
    function function_enabled($func)
    {
        // cache the list of disabled functions
        static $disabled = null;
        if ($disabled === null) $disabled = array_map('trim', array_map('strtolower', explode(',', ini_get('disable_functions'))));
        // cache the list of functions blacklisted by suhosin
        static $blacklist = null;
        if ($blacklist === null) $blacklist = extension_loaded('suhosin') ? array_map('trim', array_map('strtolower', explode(',', ini_get('  suhosin.executor.func.blacklist')))) : array();

        // checks if the function is really enabled
        return (function_exists($func) AND !in_array($func, $disabled) AND !in_array($func, $blacklist));
    }
}