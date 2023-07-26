<?php
    namespace App\Utils;

    class Page {
        private static function getComponent($type, $file) {
            if($file === false) return '';
            
            if(!$file) $file = $type;
            return View::render('pages/' . $file);
        }
        
        public static function getPage($title, $content, $opts = []) {
            $header = 'header';
            $footer = 'footer';
            
            if(isset($opts['header'])) {
                $header = $opts['header'];
                unset($opts['header']);
            }

            if(isset($opts['footer'])) {
                $footer = $opts['footer'];
                unset($opts['footer']);
            }

            return View::render('pages/page', array_merge(
                [
                    'title' => $title,
                    'header' => self::getComponent('header', $header),
                    'content' => $content,
                    'footer' => self::getComponent('footer', $footer),
                ],
                $opts
            ));
        }
    }