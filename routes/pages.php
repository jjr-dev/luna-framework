<?php
    use \App\Controllers\Pages;

    $router->get('/', [
        'middlewares' => ['maintenance'],
        function($request, $response) {
            return Pages\Example::getPage($request, $response);
        }
    ]);