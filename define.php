<?php
    use \App\Common\Environment;
    use \App\Utils\View;
    use \App\Utils\Flash;

    Environment::load(__DIR__);
    
    define("URL", getenv('URL'));

    View::define([
        'URL'     => URL,
        'PUBLIC'  => URL . '/public'
    ]);

    Flash::define([
        'FLASH_ERROR'   => 'error',
        'FLASH_DANGER'  => 'danger',
        'FLASH_WARNING' => 'warning',
        'FLASH_INFO'    => 'info',
        'FLASH_SUCCESS' => 'success'
    ]);