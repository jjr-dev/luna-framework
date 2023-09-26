<?php
    namespace Luna\Utils;

    use Illuminate\Database\QueryException;
    use Exception;
    
    class Service {
        public static function exception($e) {
            if($e instanceof QueryException) throw new Exception(Environment::get("QUERY_ERROR_MESSAGE") ?? "Erro interno", Environment::get("QUERY_ERROR_CODE") ?? 500);
            else throw new Exception($e->getMessage(), $e->getCode());
        }
    }