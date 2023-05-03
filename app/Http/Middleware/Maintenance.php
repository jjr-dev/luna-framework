<?php
    namespace App\Http\Middleware;

    class Maintenance {
        public function handle($request, $next) {
            if(getenv('MAINTENANCE') == 'true')
                throw new \Exception("Página em manutenção.", 200);
            
            return $next($request);
        }
    }