<?php

namespace Luna\Utils;

class Controller
{
    private static function getComponent(string $type, string|bool $file): string
    {
        if ($file === false) {
            return '';
        }
        
        if (!$file) {
            $file = $type;
        }
        
        return View::render($file);
    }
    
    public static function page(string $title, string $content, array $opts = []): string
    {
        $header = 'header';
        $footer = 'footer';
        $page = 'page';
        
        if (isset($opts['header'])) {
            $header = $opts['header'];
            unset($opts['header']);
        }

        if (isset($opts['footer'])) {
            $footer = $opts['footer'];
            unset($opts['footer']);
        }

        if (isset($opts['page'])) {
            $page = $opts['page'];
            unset($opts['page']);
        }

        return View::render($page, array_merge(
            [
                'title' => $title,
                'header' => self::getComponent('header', $header),
                'content' => $content,
                'footer' => self::getComponent('footer', $footer),
            ],
            $opts
        ));
    }

    public static function success(array $data = [], int $code = 200)
    {
        return [
            'data' => $data,
            'status' => $code ?: 200,
            'error' => false
        ];
    }

    public static function error(array $data = [], int $code = 400)
    {
        return [
            'data' => $data,
            'status' => $code ?: 400,
            'error' => true
        ];
    }
}