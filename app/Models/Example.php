<?php

    namespace App\Models;

    use \App\Db\Database;
    use \Illuminate\Database\Eloquent\Model;

    class Example extends Model {
        protected $table = 'tb_example';
        protected $primaryKey = 'cd_example';

        private $aliases = [
            'id'    => 'cd_example',
            'name'  => 'nm_example'
        ];

        public function __get($key) {
            return $this->getAttribute($this->aliases[$key] ?? $key);
        }
    }