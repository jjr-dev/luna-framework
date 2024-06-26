<?php
namespace Luna\Utils;

class Component {
    private static function getContentComponent($component) {
        $file = Environment::get("__DIR__") . '/resources/components/' . $component . '.html';
        return file_exists($file) ? file_get_contents($file) : '';
    }
    
    public static function render($component, $vars = []) {
        $contentComponent = self::getContentComponent($component);
        return View::render(false, $vars, $contentComponent);
    }

    public static function multiRender($component, $vars = []) {
        $contentComponents = [];
        foreach($vars as $var) {
            $contentComponents[] = self::render($component, $var);
        }
        return $contentComponents ? implode("", $contentComponents) : [];
    }
}