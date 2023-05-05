<?php
    use \App\Http\Response;
    use \App\Controllers\Pages;

    $router->get('/', [
        function($request, $response) {
            return Pages\Example::getPage($request, $response);
        }
    ]);