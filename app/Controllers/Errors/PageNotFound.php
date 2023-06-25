<?php
    namespace App\Controllers\Errors;

    use \App\Utils\View;
    use \App\Utils\Seo;
    use \App\Utils\Page;

    class PageNotFound extends Page {
        public static function notFoundPage($req, $res) {
            $title = 'Luna - Página não encontrada';
            
            $seo = new Seo();
            $seo->setRobots(false, false);
            $seo = $seo->render();

            $content = View::render('errors/404');
            $content = parent::getPage($title, $content, ['seo' => $seo]);
            return $res->send(404, $content);
        }
    }