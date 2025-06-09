<?php

namespace Luna\Utils;

class Component
{
    private static function getContentComponent(string $component): string
    {
        $file = Environment::get("__DIR__") . '/resources/components/' . $component . '.html';

        if (!file_exists($file)) {
            return "";
        }
        
        return file_get_contents($file);
    }
    
    public static function render(string $component, array|object $vars = []): string
    {
        $contentComponent = self::getContentComponent($component);
        
        return View::render(false, $vars, $contentComponent);
    }

    public static function multiRender(string $component, array|object $vars = []): array|string
    {
        $contentComponents = [];

        foreach ($vars as $var) {
            $contentComponents[] = self::render($component, $var);
        }
        
        return $contentComponents ? implode("", $contentComponents) : [];
    }
}