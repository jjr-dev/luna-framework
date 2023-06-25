<?php
    namespace App\Utils;

    class Page {
        private static function getComponent($type, $file) {
            if($file === false) return '';
            
            if(!$file) $file = $type;
            return View::render('pages/' . $file);
        }
        
        public static function getPage($title, $content, $opts = []) {
            $header = isset($opts['header']) ? $opts['header'] : 'header';
            $footer = isset($opts['footer']) ? $opts['footer'] : 'footer';
            $seo = isset($opts['seo']) ? $opts['seo'] : '';

            return View::render('pages/page', [
                'title' => $title,
                'header' => self::getComponent('header', $header),
                'content' => $content,
                'footer' => self::getComponent('footer', $footer),
                'seo' => $seo
            ]);
        }
    }