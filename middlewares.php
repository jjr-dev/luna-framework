<?php
    use \App\Http\Middleware\Queue AS MiddlewareQueue;

    MiddlewareQueue::setMap([
        'maintenance' => \App\Http\Middleware\Maintenance::class
    ]);

    MiddlewareQueue::setDefault([
        'maintenance'
    ]);