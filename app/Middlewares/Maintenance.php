<?php
    namespace App\Middlewares;

    class Maintenance {
        public function handle($request, $response, $next) {
            if(false)
                return $response->send(200, "Em manutenção");
    
            return $next($request, $response);
        }
    }