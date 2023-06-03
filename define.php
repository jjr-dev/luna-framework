<?php
    use \App\Common\Environment as Env;
    use \App\Utils\View;
    use \App\Utils\Flash;

    Env::load(__DIR__);
    
    define("URL", Env::get('URL'));

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