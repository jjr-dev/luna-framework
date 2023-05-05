<?php
    use \App\Controllers\Pages;

    $router->get('/', [
        function($request, $response) {
            return Pages\Example::getPage($request, $response);
        }
    ]);