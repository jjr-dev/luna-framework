<?php
    namespace Luna\Utils;

    class Environment {
        public static function load($dir = false) {
            if(!$dir) $dir = Environment::get("__DIR__");
            $path = $dir . '/.env';
            
            if(!file_exists($path))
                return false;

            $lines = file($path);
            foreach($lines as $line) {
                $line = trim($line);
                if($line === "" || strpos($line, "=") === false) continue;

                list($key, $value) = explode("=", $line, 2);

                if (preg_match('/^(\'|")(.*)\1$/', $value, $matches))
                    $value = $matches[2];
                
                if(!empty($line)) self::set($key, $value);
            }
        }

        public static function get($key = false) {
            return $key ? $_ENV[$key] : $_ENV;
        }

        public static function set($key, $value) {
            $_ENV[$key] = $value;
        }
    }