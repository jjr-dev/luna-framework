<?php
    namespace Luna\Utils;

    class Controller {
        private static function getComponent($type, $file) {
            if($file === false) return '';
            
            if(!$file) $file = $type;
            return View::render($file);
        }
        
        public static function page($title, $content, $opts = []) {
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

            return View::render('page', array_merge(
                [
                    'title' => $title,
                    'header' => self::getComponent('header', $header),
                    'content' => $content,
                    'footer' => self::getComponent('footer', $footer),
                ],
                $opts
            ));
        }

        public static function success($data, $code = 200) {
            return ['data' => $data, 'code' => $code];
        }

        public static function error($error, $code = 400) {
            return ['error' => $error, 'code' => $code];
        }
    }