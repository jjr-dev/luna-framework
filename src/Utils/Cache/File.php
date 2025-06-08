<?php

namespace Luna\Utils\Cache;

use Luna\Utils\Environment;

class File
{
    private static function getFilePath(string $hash): string
    {
        $dir = Environment::get('CACHE_DIR');
        
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }

        return $dir . '/' . $hash;
    }

    private static function storageCache(string $hash, string $content): int|false
    {
        $serialized = serialize($content);
        
        $cacheFile = self::getFilePath($hash);

        return file_put_contents($cacheFile, $serialized);
    }

    private static function getContentCache(string $hash, int $expiration): string|bool
    {
        $cacheFile = self::getFilePath($hash);

        if (!file_exists($cacheFile)) {
            return false;
        }

        $createTime = filemtime ($cacheFile);
        $diffTime = (time() - $createTime) * 1000;

        if ($diffTime > $expiration) {
            return false;
        }

        $serialized = file_get_contents($cacheFile);
        
        return unserialize($serialized);
    }

    public static function getCache(string $hash, int $expiration, callable $function): string
    {
        if ($content = self::getContentCache($hash, $expiration)) {
            return $content;
        }

        $content = $function();

        self::storageCache($hash, $content);

        return $content;
    }
}