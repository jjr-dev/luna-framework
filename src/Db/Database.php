<?php
namespace Luna\Db;

use Luna\Utils\Environment;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

class Database {
    private static $configs = [];
    
    private static function setConfigs() {
        self::$configs = [
            'driver'    => Environment::get('DB_DRIVER'),
            'host'      => Environment::get('DB_HOST'),
            'port'      => Environment::get('DB_PORT'),
            'database'  => Environment::get('DB_NAME'),
            'username'  => Environment::get('DB_USER'),
            'password'  => Environment::get('DB_PASS'),
            'charset'   => Environment::get('DB_CHARSET'),
            'collation' => Environment::get('DB_COLLATION'),
            'prefix'    => Environment::get('DB_PREFIX')
        ];
    }

    public static function boot() {
        self::setConfigs();
        
        $capsule = new Capsule;
        $capsule->addConnection(self::$configs);
        $capsule->setAsGlobal();
        $capsule->bootEloquent();

        if(!Capsule::schema()->hasTable('migrations')) {
            Capsule::schema()->create('migrations', function (Blueprint $table) {
                $table->increments('id');
                $table->text('filename');
                $table->integer('batch');
            });
        }

        if(!Capsule::schema()->hasTable('logs')) {
            Capsule::schema()->create('logs', function (Blueprint $table) {
                $table->id();
                $table->string('public_id');
                $table->text('message');
                $table->tinyText('code')->nullable();
                $table->timestamp('created_at');
            });
        }
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