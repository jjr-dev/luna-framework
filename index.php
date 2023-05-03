<?php
    require __DIR__ . '/vendor/autoload.php';
    require __DIR__ . '/middlewares.php';

    use \App\Http\Router;
    use \App\Utils\View;
    use \App\Common\Environment;
    use \App\Db\Database;
    
    Environment::load(__DIR__);

    Database::boot();

    define("URL", getenv('URL'));

    View::init([
        'URL' => URL
    ]);

    $obRouter = new Router(URL);

    include __DIR__ . '/routes/pages.php';

    $obRouter->run()->sendResponse();