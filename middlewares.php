<?php
    use \App\Http\Middleware;

    Middleware::setMap([
        'maintenance' => \App\Middlewares\Maintenance::class
    ]);

    Middleware::setDefault([]);