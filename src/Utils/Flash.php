<?php

namespace Luna\Utils;

class Flash
{
    public static function create(string $name, string $message, string $type, string|bool $component = false): void
    {
        session_start();
        
        if (isset($_SESSION['FLASH_MESSAGES'][$name])) {
            unset($_SESSION['FLASH_MESSAGES'][$name]);
        }

        $_SESSION['FLASH_MESSAGES'][$name] = self::setArray($message, $type, $component);
    }

    public static function render(string $name, string|bool $message = false, string|bool $type = false, string|bool $component = false): string
    {
        $flash = $name && !$message ? $_SESSION['FLASH_MESSAGES'][$name] : self::setArray($message, $type, $component);
        return self::renderComponent($flash, $component);
    }

    public static function renderAll(array $flashs): string
    {
        $rendereds = [];

        foreach ($flashs as $flash) {
            array_push($rendereds, self::render($flash));
        }

        return implode('', $rendereds);
    }

    private static function setArray(string $message, string $type, string $component): array
    {
        return [
            'component' => $component,
            'message' => $message,
            'type' => $type
        ];
    }

    private static function renderComponent(array $flash): string
    {
        if (!$flash['component']) {
            $flash['component'] = 'alert';
        }

        return Component::render('flash/' . $flash['component'], [
            'message' => $flash['message'],
            'type' => $flash['type']
        ]);
    }

    public static function list(): array
    {
        return $_SESSION['FLASH_MESSAGES'];
    }
}