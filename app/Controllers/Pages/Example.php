<?php
    namespace App\Controllers\Pages;

    use \App\Utils\View;

    class Example extends Page {
        public static function getPage($req, $res) {
            $content = View::render('pages/example');
            $content = parent::getPage("MVC Example", $content);
            
            return $res->send(200, $content);
        }
    }