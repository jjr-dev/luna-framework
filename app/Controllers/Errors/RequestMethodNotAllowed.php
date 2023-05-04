<?php
    namespace App\Controllers\Errors;

    use \App\Utils\View;
    use \App\Controllers\Pages\Page;

    class RequestMethodNotAllowed extends Page {
        public static function getPage($request) {
            $content = View::render('errors/405');

            return parent::getPage("Erro 405", $content);
        }
    }