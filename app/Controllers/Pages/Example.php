<?php
    namespace App\Controllers\Pages;

    use \App\Utils\View;

    class Example extends Page {
        public static function getPage($request) {
            $content = View::render('pages/example');
            return parent::getPage("MVC Example", $content);
        }
    }