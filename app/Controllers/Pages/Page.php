<?php
    namespace App\Controllers\Pages;

    use \App\Utils\View;

    class Page {
        private static function getComponent($type, $file) {
            if($file === false) return '';
            
            if(!$file) $file = $type;
            return View::render('pages/' . $file);
        }
        
        public static function getPage($title, $content, $header = null, $footer = null) {
            return View::render('pages/page', [
                'title' => $title,
                'header' => self::getComponent('header', $header),
                'content' => $content,
                'footer' => self::getComponent('footer', $footer)
            ]);
        }
    }