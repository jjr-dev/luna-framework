<?php
    namespace App\Db;

    use \App\Utils\Environment as Env;
    use \Illuminate\Database\Capsule\Manager as Capsule;

    class Database {
        private static $configs = [];
        
        private static function setConfigs() {
            $env = Env::get();
            
            self::$configs = [
                'driver'    => $env['DB_DRIVER'],
                'host'      => $env['DB_HOST'],
                'database'  => $env['DB_NAME'],
                'username'  => $env['DB_USER'],
                'password'  => $env['DB_PASS'],
                'charset'   => 'utf8',
                'collation' => 'utf8_unicode_ci',
                'prefix'    => ''
            ];
        }

        public static function boot() {
            self::setConfigs();
            
            $capsule = new Capsule;
            $capsule->addConnection(self::$configs);
            $capsule->bootEloquent();
        }
    }