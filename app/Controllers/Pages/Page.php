<?php
    namespace App\Controllers\Pages;

    use \App\Utils\View;

    class Page {
        private static function getHeader($file) {
            if(!$file) $file = 'header';
            return View::render('pages/' . $file);
        }

        private static function getFooter($file) {
            if(!$file) $file = 'footer';
            return View::render('pages/' . $file);
        }
        
        public static function getPage($title, $content, $header = false, $footer = false) {
            return View::render('pages/page', [
                'title' => $title,
                'header' => self::getHeader($header),
                'content' => $content,
                'footer' => self::getFooter($footer)
            ]);
        }
    }