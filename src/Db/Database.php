<?php
    namespace Luna\Db;

    use Luna\Utils\Environment;
    use Illuminate\Database\Capsule\Manager as Capsule;

    class Database {
        private static $configs = [];
        
        private static function setConfigs() {
            $env = Environment::get();
            
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

        public static function boot($global = false) {
            self::setConfigs();
            
            $capsule = new Capsule;
            $capsule->addConnection(self::$configs);
            $capsule->bootEloquent();

            if($global) $capsule->setAsGlobal();
        }

        public static function queryToSql($query, $replaceBindings = false) {
            $sql = $query->toSql();

            if($replaceBindings)
                foreach ($query->getBindings() as $binding) {
                    $sql = preg_replace('/\?/', "'$binding'", $sql, 1);
                }
            
            return $sql;
        }
    }