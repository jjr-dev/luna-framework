<?php
    namespace App\Utils;

    class View {
        protected static $vars = [];

        public static function define($vars = []) {
            self::$vars = $vars;
        }

        private static function getContentView($view) {
            $file = __DIR__ . '/../../resources/views/' . $view . '.html';
            return file_exists($file) ? file_get_contents($file) : '';
        }

        private static function organizeVars($vars) {
            $hasArray = false;

            foreach($vars as $varsKey => $varsValue) {
                if(is_object($varsValue))
                    $varsValue = (array) $varsValue;
                    
                if(!is_array($varsValue))
                    continue;

                foreach($varsValue as $varKey => $varValue) {
                    $vars[$varsKey . '->' . $varKey] = $varValue;
                }
                
                $hasArray = true;
                unset($vars[$varsKey]);
            }

            if($hasArray)
                $vars = self::organizeVars($vars);

            return $vars;
        }

        public static function render($view, $vars = [], $content = false, $removeEmptyVars = true) {
            $contentView = $content ? $content : self::getContentView($view);

            $vars = array_merge(self::$vars, $vars);
            $vars = self::organizeVars($vars);

            $keys = array_keys($vars);
            $keys = array_map(function($item) {
                return '{{' . $item . '}}';
            }, $keys);

            $content = str_replace($keys, array_values($vars), $contentView);
            return $removeEmptyVars ? preg_replace('/{{.*?}}/', '', $content) : $content;
        }
    }