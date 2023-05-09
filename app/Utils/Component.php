<?php
    namespace App\Utils;

    class Component {
        private static function getContentComponent($component) {
            $file = __DIR__ . '/../../resources/components/' . $component . '.html';
            return file_exists($file) ? file_get_contents($file) : '';
        }
        
        public static function render($component, $vars = []) {
            $contentComponent = self::getContentComponent($component);
            return View::render(false, $vars, $contentComponent);
        }
    }