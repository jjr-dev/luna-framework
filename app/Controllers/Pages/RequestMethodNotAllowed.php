<?php
    namespace App\Controllers\Pages;

    use \App\Utils\View;

    class RequestMethodNotAllowed extends Page {
        public static function getPage($request) {
            $content = View::render('errors/405');

            return parent::getPage("Erro 405", $content);
        }
    }