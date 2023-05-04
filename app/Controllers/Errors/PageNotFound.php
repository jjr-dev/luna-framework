<?php
    namespace App\Controllers\Errors;

    use \App\Utils\View;
    use \App\Controllers\Pages\Page;

    class PageNotFound extends Page {
        public static function getPage($request) {
            $content = View::render('errors/404');
            return parent::getPage("Erro 404", $content);
        }
    }