<?php
    use \App\Http\Response;
    use \App\Controllers\Errors;

    $router->error(404, [
        function($request) {
            return new Response(404, Errors\PageNotFound::getPage($request));
        }
    ]);