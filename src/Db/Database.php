<?php
    namespace Luna\Db;

    use Luna\Utils\Environment;
    use Illuminate\Database\Capsule\Manager as Capsule;

    class Database {
        private static $configs = [];
        
        private static function setConfigs() {
            self::$configs = [
                'driver'    => Environment::get('DB_DRIVER'),
                'host'      => Environment::get('DB_HOST'),
                'database'  => Environment::get('DB_NAME'),
                'username'  => Environment::get('DB_USER'),
                'password'  => Environment::get('DB_PASS'),
                'charset'   => Environment::get('DB_CHARSET'),
                'collation' => Environment::get('DB_COLLATION'),
                'prefix'    => Environment::get('DB_PREFIX')
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