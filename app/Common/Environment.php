<?php

    namespace App\Common;

    class Environment {
        public static function load($dir) {
            $path = $dir . '/.env';
            
            if(!file_exists($path))
                return false;

            $lines = file($path);
            foreach($lines as $line) {
                putenv(trim($line));
            }
        }
    }