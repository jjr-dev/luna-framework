<?php
    namespace Luna\Utils;

    use Illuminate\Database\QueryException;
    use Exception;
    
    class Service {
        public static function exception($e, $class = false) {
            $code = $e->getCode();
            $message = $e->getMessage();
            
            if($e instanceof QueryException) {
                $publicId = false;
                
                if(Environment::get("LOG_QUERY_ERROR_MESSAGE")) {
                    $inFileErrors = ['2002', '1045'];

                    if(!in_array($code, $inFileErrors)) {
                        try {
                            $publicId = Log::save($message, $code);
                        } catch(Exception $ee) {
                            error_log($ee->getCode() . ' - ' . $ee->getMessage());
                        }
                    } else {
                        error_log($code . ' - ' . $message);
                    }
                }

                throw new Exception((Environment::get("QUERY_ERROR_MESSAGE") ?? "Erro interno") . ($publicId ? " - " . $publicId : ""), Environment::get("QUERY_ERROR_CODE") ?? 500);
            }

            if($class) {
                if(!is_array($class)) $class = [$class];

                foreach($class as $c) {
                    if($e instanceof $e)
                        throw new $c($message, $code);
                }
            }
            
            throw $e;
        }
    }