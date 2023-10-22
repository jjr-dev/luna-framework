<?php
    namespace Luna\Utils;

    class View {
        protected static $vars = [];

        public static function define($vars = []) {
            self::$vars = $vars;
        }

        private static function getContentView($view) {
            $file = Environment::get("__DIR__") . '/resources/views/' . $view . '.html';
            return file_exists($file) ? file_get_contents($file) : '';
        }

        private static function organizeVars($vars) {
            $hasArray = false;

            foreach($vars as $varsKey => $varsValue) {
                if(is_object($varsValue))
                    $varsValue = (array) $varsValue;

                if($varsValue === NULL)
                    unset($vars[$varsKey]);
                    
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

        private static function organizeCoalescence($content) {
            $pattern = '/{{(.*?)\?\?(.*?)}}/';
            preg_match_all($pattern, $content, $matches);

            if(!$matches || count($matches[0]) == 0) return false;
            $matches = $matches[0];
            
            $coalescences = [];
            foreach($matches as $item) {
                $item = substr($item, 2, -2);

                list($key, $value) = explode("??", $item, 2);
                if(!$key || !$value) continue;

                $coalescences[trim($key)] = trim($value);
            }

            return $coalescences;
        }

        private static function removeNewlinesInsideBraces($input) {
            $pattern = '/\{\{(.*?)\}\}/s';
            $output = preg_replace_callback($pattern, function($matches) {
                $block = rtrim(preg_replace('/\s+/', ' ', $matches[1]));
                if(substr($block, -2) === "??") $block .= " ";
                return '{{' . $block . '}}';
            }, $input);
        
            return $output;
        }

        public static function render($view, $vars = [], $content = false) {
            $contentView = $content ? $content : self::getContentView($view);
            $contentView = self::removeNewlinesInsideBraces($contentView);

            $coalescences = self::organizeCoalescence($contentView);
            if($coalescences) {
                $coalescencesPreKeys = array_keys($coalescences);

                $coalescencesKeys = array_map(function($item) {
                    return '{{' . $item . '}}';
                }, $coalescencesPreKeys);

                $coalescencesPreKeys = array_map(function($item) use ($coalescences) {
                    return '{{' . $item . ' ?? ' . $coalescences[$item] . '}}';
                }, $coalescencesPreKeys);

                $contentView = str_replace($coalescencesPreKeys, $coalescencesKeys, $contentView);
            }

            if(is_object($vars) && method_exists($vars, 'toArray'))
                $vars = $vars->toArray();

            $vars = array_merge(self::$vars, $vars);
            $vars = self::organizeVars($vars);

            $keys = array_keys($vars);
            $keys = array_map(function($item) {
                return '{{' . $item . '}}';
            }, $keys);

            $content = str_replace($keys, array_values($vars), $contentView);

            if($coalescences)
                $content = str_replace($coalescencesKeys, array_values($coalescences), $content);

            return $content;
        }
    }