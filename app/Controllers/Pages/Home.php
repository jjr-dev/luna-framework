<?php
    namespace App\Controllers\Pages;

    use \App\Utils\View;

    class Home extends Page {
        public static function getPage($req, $res) {
            $content = View::render('pages/home');
            $content = parent::getPage("Luna - Framework MVC", $content);
            
            return $res->send(200, $content);
        }
    }