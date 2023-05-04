<?php
    use \App\Http\Response;
    use \App\Controllers\Pages;

    $router->error(404, [
        function($request) {
            return new Response(404, Pages\PageNotFound::getPage($request));
        }
    ]);

    $router->error(405, [
        function($request) {
            return new Response(404, Pages\RequestMethodNotAllowed::getPage($request));
        }
    ]);