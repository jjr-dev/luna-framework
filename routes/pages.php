<?php
    use \App\Http\Response;
    use \App\Controllers\Pages;

    $router->get('/', [
        function($request) {
            return new Response(200, Pages\Example::getPage($request));
        }
    ]);