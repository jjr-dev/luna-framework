<?php
    use \App\Controllers\Pages;

    $router->get('/', [
        function($request, $response) {
            return Pages\Home::homePage($request, $response);
        }
    ]);