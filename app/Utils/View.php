<?php
    namespace App\Utils;

    class View {
        protected static $vars = [];

        public static function init($vars = []) {
            self::$vars = $vars;
        }

        private static function getContentView($view) {
            $file = __DIR__ . '/../../resources/views/' . $view . '.html';
            return file_exists($file) ? file_get_contents($file) : '';
        }

        public static function render($view, $vars = [], $content = false) {
            $contentView = $content ? $content : self::getContentView($view);

            $vars = array_merge(self::$vars, $vars);

            $keys = array_keys($vars);
            $keys = array_map(function($item) {
                return '{{' . $item . '}}';
            }, $keys);

            return str_replace($keys, array_values($vars), $contentView);
        }
    }