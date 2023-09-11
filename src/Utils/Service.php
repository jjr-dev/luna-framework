<?php
    namespace Luna\Utils;
    
    class Service {
        public static function exception($e) {
            if($e instanceof \Illuminate\Database\QueryException) throw new \Exception("Erro interno", 500);
            else throw new \Exception($e->getMessage(), $e->getCode());
        }
    }