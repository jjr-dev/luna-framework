<?php

    namespace App\Http;

    class Middleware {
        private static $core = [
            'cache' => \App\Middlewares\Cache::class
        ];
        
        private static $map = [];
        private static $default = [];

        private $middlewares = [];
        private $controller;
        private $controllerArgs = [];

        public function __construct($middlewares, $controller, $controllerArgs) {
            $this->middlewares    = array_merge(self::$default, $middlewares);
            $this->controller     = $controller;
            $this->controllerArgs = $controllerArgs;
        }

        public static function setMap($map) {
            self::$map = array_merge(self::$core, $map);
        }

        public static function setDefault($default) {
            self::$default = $default;
        }

        public function next($request, $response) {
            if(empty($this->middlewares)) return call_user_func_array($this->controller, $this->controllerArgs);

            $middleware = array_shift($this->middlewares);

            if(!isset(self::$map[$middleware]))
                throw new \Exception("Problemas ao processar o Middleware", 500);
                
            $queue = $this;
            $next  = function($request) use($queue) {
                return $queue->next($request, $response);
            };

            return (new self::$map[$middleware])->handle($request, $response, $next);
        }
    }