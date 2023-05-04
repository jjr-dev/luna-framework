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
        'URL'    => URL,
        'PUBLIC' => URL . '/public'
    ]);

    $router = new Router(URL);

    $routerDir = dir(__DIR__ . '/routes');
    while(($file = $routerDir->read()) !== false) {
        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        if($ext === 'php')
            include __DIR__ . '/routes/' . $file;
    }
    $routerDir->close();

    $router->run()->sendResponse();