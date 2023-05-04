<?php
    namespace App\Controllers\Pages;

    use \App\Utils\View;

    class PageNotFound extends Page {
        public static function getPage($request) {
            $content = View::render('errors/404');

            return parent::getPage("Erro 404", $content);
        }
    }