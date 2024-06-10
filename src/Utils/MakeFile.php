<?php
namespace Luna\Utils;

class MakeFile {
    public static function make($path, $content) {
        $dirPath = dirname($path);

        if(!is_dir($dirPath))
            mkdir($dirPath, 0755, true);

        return file_put_contents($path, is_array($content) ? self::arrayToText($content) : $content);
    }

    private static function arrayToText($arr) {
        return implode(PHP_EOL, $arr);
    }
}