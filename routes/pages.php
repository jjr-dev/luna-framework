<?php
    use \App\Http\Response;
    use \App\Controller\Pages;

    $obRouter->get('/', [
        function() {
            return new Response(200, Pages\Home::getHome());
        }
    ]);

    $obRouter->get('/about', [
        function() {
            return new Response(200, Pages\About::getAbout());
        }
    ]);

    $obRouter->get('/pagina/{idPagina}/{acao}', [
        function($idPagina, $acao) {
            return new Response(200, 'Página ' . $idPagina . ' - ' . $acao);
        }
    ]);