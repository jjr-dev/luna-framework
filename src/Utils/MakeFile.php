<?php

namespace Luna\Utils;

class MakeFile
{
    public static function make(string $path, string|array $content): int|false
    {
        $dirPath = dirname($path);

        if (!is_dir($dirPath)) {
            mkdir($dirPath, 0755, true);
        }

        return file_put_contents($path, is_array($content) ? self::arrayToText($content) : $content);
    }

    private static function arrayToText(array $arr): string
    {
        return implode(PHP_EOL, $arr);
    }
}