<?php

namespace Luna\Db;

use Luna\Utils\Environment;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Schema\Blueprint;

class Database
{
    private static array $configs = [];
    
    private static function setConfigs(): void
    {
        self::$configs = [
            'driver' => Environment::get('DB_DRIVER'),
            'host' => Environment::get('DB_HOST'),
            'port' => Environment::get('DB_PORT'),
            'database' => Environment::get('DB_NAME'),
            'username' => Environment::get('DB_USER'),
            'password' => Environment::get('DB_PASS'),
            'charset' => Environment::get('DB_CHARSET'),
            'collation' => Environment::get('DB_COLLATION'),
            'prefix' => Environment::get('DB_PREFIX')
        ];
    }

    public static function boot(): void
    {
        self::setConfigs();
        
        $capsule = new Manager;
        $capsule->addConnection(self::$configs);
        $capsule->setAsGlobal();
        $capsule->bootEloquent();

        if (!Manager::schema()->hasTable('migrations')) {
            Manager::schema()->create('migrations', function(Blueprint $table) {
                $table->increments('id');
                $table->text('filename');
                $table->integer('batch');
            });
        }

        if (!Manager::schema()->hasTable('logs')) {
            Manager::schema()->create('logs', function(Blueprint $table) {
                $table->id();
                $table->string('public_id');
                $table->text('message');
                $table->tinyText('code')->nullable();
                $table->timestamp('created_at');
            });
        }
    }

    public static function queryToSql(object $query, bool $replaceBindings = false): string
    {
        $sql = $query->toSql();

        if ($replaceBindings) {
            foreach ($query->getBindings() as $binding) {
                $sql = preg_replace('/\?/', "'$binding'", $sql, 1);
            }
        }
        
        return $sql;
    }
}