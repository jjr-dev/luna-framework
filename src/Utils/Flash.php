<?php
    namespace Luna\Utils;

    class Flash {
        public static function create($name, $message, $type, $component = false) {
            session_start();
            
            if(isset($_SESSION['FLASH_MESSAGES'][$name]))
                unset($_SESSION['FLASH_MESSAGES'][$name]);

            $_SESSION['FLASH_MESSAGES'][$name] = self::setArray($message, $type, $component);
        }

        public static function render($name, $message = false, $type = false, $component = false) {
            $flash = $name && !$message ? $_SESSION['FLASH_MESSAGES'][$name] : self::setArray($message, $type, $component);
            return self::renderComponent($flash, $component);
        }

        public static function renderAll($flashs) {
            $rendereds = [];

            foreach($flashs as $flash) {
                array_push($rendereds, self::render($flash));
            }

            return implode('', $rendereds);
        }

        private static function setArray($message, $type, $component) {
            return ['message' => $message, 'type' => $type, 'component' => $component];
        }

        private static function renderComponent($flash) {
            if(!$flash['component'])
                $flash['component'] = 'alert';

            return Component::render('flash/' . $flash['component'], [
                'message' => $flash['message'],
                'type' => $flash['type']
            ]);
        }

        public static function list() {
            return $_SESSION['FLASH_MESSAGES'];
        }
    }