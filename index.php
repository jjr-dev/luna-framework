<?php
    require __DIR__ . '/vendor/autoload.php';
    require __DIR__ . '/middlewares.php';
    require __DIR__ . '/define.php';

    use \App\Http\Router;
    use \App\Db\Database;
    
    Database::boot();

    $router = new Router(URL);

    $routerDir = dir(__DIR__ . '/routes');
    while(($file = $routerDir->read()) !== false) {
        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        if($ext === 'php')
            include __DIR__ . '/routes/' . $file;
    }
    $routerDir->close();

    $router->run()->sendResponse();