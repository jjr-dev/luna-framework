<?php
    namespace App\Http\Middleware;

    class Maintenance {
        public function handle($req, $res, $next) {
            if(false)
                return $res->send(200, "Em manutenção");

            return $next($req, $res);
        }
    }