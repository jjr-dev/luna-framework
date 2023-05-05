<?php
    use \App\Controllers\Errors;

    $router->error(404, [
        function($request, $response) {
            return Errors\PageNotFound::getPage($request, $response);
        }
    ]);