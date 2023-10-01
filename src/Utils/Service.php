<?php
    namespace Luna\Utils;

    use Illuminate\Database\QueryException;
    use Exception;
    
    class Service {
        public static function exception($e, $class = false) {
            if($e instanceof QueryException)
                throw new Exception(Environment::get("QUERY_ERROR_MESSAGE") ?? "Erro interno", Environment::get("QUERY_ERROR_CODE") ?? 500);

            if($class) {
                if(!is_array($class)) $class = [$class];

                foreach($class as $c) {
                    if($e instanceof $e)
                        throw new $c($e->getmessage(), $e->getCode());
                }
            }
            
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }