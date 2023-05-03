<?php

    namespace App\Models;

    use \App\Db\Database;
    use \Illuminate\Database\Eloquent\Model;

    class Organization extends Model {
        protected $table = 'tb_organization';
        protected $primaryKey = 'cd_organization';

        private $aliases = [
            'id'    => 'cd_organization',
            'name'  => 'nm_organization',
            'description' => 'ds_organization'
        ];

        public function __get($key) {
            return $this->getAttribute($this->aliases[$key] ?? $key);
        }
    }