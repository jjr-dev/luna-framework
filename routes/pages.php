<?php
    use \App\Http\Response;
    use \App\Controllers\Pages;

    $router->get('/', [
        'middlewares' => [
            'maintenance'
        ],
        function($request) {
            return new Response(200, Pages\Home::getHome($request));
        }
    ]);

    $router->get('/about', [
        function() {
            return new Response(200, Pages\About::getAbout());
        }
    ]);

    $router->get('/pagina/{id}/{action}', [
        'middlewares' => [
            'maintenance'
        ],
        function($request) {
            $pathParams =  $request->getPathParams();
            return new Response(200, 'Página ' . $pathParams['id'] . ' - ' . $pathParams['action']);
        }
    ]);