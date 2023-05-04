<?php
    use \App\Http\Response;
    use \App\Controllers\Pages;

    $router->error('default', [
        function($request) {
            return new Response(500, 'Erro geral');
        }
    ]);

    $router->error(404, [
        function($request) {
            return new Response(404, Pages\PageNotFound::getPage($request));
        }
    ]);

    $router->error(405, [
        function($request) {
            return new Response(405, Pages\RequestMethodNotAllowed::getPage($request));
        }
    ]);

    $router->error(500, [
        function($request) {
            return new Response(500, 'Erro 500');
        }
    ]);