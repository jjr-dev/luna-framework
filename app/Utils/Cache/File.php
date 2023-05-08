<?php
    
    namespace App\Utils\Cache;

    class File {
        private static function getFilePath($hash) {
            $dir = getenv('CACHE_DIR');
            
            if(!file_exists($dir)) {
                mkdir($dir, 0755, true);
            }

            return $dir . '/' . $hash;
        }

        private static function storageCache($hash, $content) {
            $serialized = serialize($content);
            
            $cacheFile = self::getFilePath($hash);

            return file_put_contents($cacheFile, $serialized);
        }

        private static function getContentCache($hash, $expiration) {
            $cacheFile = self::getFilePath($hash);

            if(!file_exists($cacheFile)) return false;

            $createTime = filemtime ($cacheFile);
            $diffTime = (time() - $createTime) * 1000;

            if($diffTime > $expiration) return false;

            $serialized = file_get_contents($cacheFile);
            
            return unserialize($serialized);
        }

        public static function getCache($hash, $expiration, $function) {
            if($content = self::getContentCache($hash, $expiration))
                return $content;

            $content = $function();

            self::storageCache($hash, $content);

            return $content;
        }
    }