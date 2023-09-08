# Luna - MVC

Luna é um framework desenvolvido em PHP com inspirações em outros frameworks como Laravel, CodeIgniter e o Express do JavaScript voltado para desenvolvimento Web com recursos como:

-   Mapeamento de rotas;
-   Banco de dados em ORM (Illuminate/Eloquent);
-   Fila de middlewares;
-   Praticidade, segurança e agilidade;
-   Hospedagem simplificada;
-   Armazenamento em cache;
-   Componentização;
-   Paginação;
-   Search Engine Optimization (SEO).

# Aprendendo Luna

## Instalação

Antes de iniciar seu projeto Luna, é necessário realizar a instalação do PHP (_versão 7.1 ou superior_) e [Composer](https://getcomposer.org/).

Utilize o comando `composer require phpluna/mvc` para instalar o Luna em seu projeto.

## Inicializando

O Luna será instalado na pasta `vendor` (assim como outros pacotes do Composer), para que todos os recursos funcionem corretamente, crie um arquivo `.htaccess`:

```apacheconf
RewriteEngine on

RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^(.*)$ ./index.php [QSA,NC]
```

Com o arquivo `.htaccess` configurado, crie o arquivo `index.php`:

```php
<?php
    require __DIR__ . '/vendor/autoload.php';

    // Load Dependencies
    use Luna\Utils\View;
    use Luna\Utils\Environment;
    use Luna\Http\Middleware;
    use Luna\Http\Router;
    use Luna\Db\Database;

    // Define Root Directory
    define("ROOT_DIR", __DIR__);

    // Load Environment
    Environment::load(ROOT_DIR);

    // Define URL Global Var
    define("URL", Environment::get('URL'));

    // Define View Vars
    View::define(['URL' => URL]);

    // Boot Database
    Database::boot();

    // Define Middlewares
    Middleware::setMap([]);
    Middleware::setDefault([]);

    // Start Routers
    new Router(URL);
```

A classe `Environment` é responsável por carregar o arquivo `.env` com as configurações base, recomenda-se a criação do mesmo no mesmo nível do `index.php` e `.htaccess`:

```shell
DB_DRIVER=mysql
DB_HOST=localhost
DB_USER=user
DB_PASS=password
DB_NAME=database
URL=http://localhost/luna
DEFAULT_CONTENT_TYPE=text/html
CACHE_TIME=100000
CACHE_DIR=./cache
ALLOW_NO_CACHE_HEADER=true
DEFAULT_SEO=twitter,meta
```

> As configurações gerais do projeto podem ser definidas no mesmo arquivo `.env`, como por exemplo a chave de autenticação de alguma API de terceiros.

## Diretórios

Recomenda-se seguir o seguinte padrão de diretórios:

```
  ├─ vendor
  ├─ app
  │   └─ Controllers
  │   └─ Services
  │   └─ Helpers
  │   └─ Models
  ├─ public
  │   └─ assets
  ├─ README.md
  ├─ resources
  │   └─ components
  │   └─ views
  └─ routes
      └─ pages.php
```

## Rotas

A criação de rota deve ser realizada em algum arquivo do diretório `/routes` (levando em consideração que você está seguindo o padrão apresentado aqui). É possível criar arquivos de separação, como `pages.php` para rotas de páginas ou `api.php` para rotas da API:

```php
<?php
    use App\Controllers\Pages;

    $router->get('/', [
        function($request, $response) {
            return Pages\Home::homePage($request, $response);
        }
    ]);
```

### Métodos de rota disponíveis

```php
$router->get($uri, [$callback]);
$router->post($uri, [$callback]);
$router->put($uri, [$callback]);
$router->patch($uri, [$callback]);
$router->delete($uri, [$callback]);
$router->options($uri, [$callback]);
```

Também é possível definir múltiplos métodos para um mesmo `$uri` e `$callback`:

```php
$router->match(['get', 'post'], $uri, [$callback]);
$router->any($uri, [$callback]);
```

### Parâmetros de rota

As rotas podem receber parâmetros personalizados:

```php
$router->get('/products/{id}', [
    function($request, $response) {
        return Pages\Product::getPage($request, $response);
    }
]);
```

Os parâmetros podem ser obtidos na função executada:

```php
class Product extends Page {
    public static function getPage($request, $response) {
        $param = $req->param(); // Parâmetros da URL
        $body = $req->body(); // Parâmetros do Corpo
        $query = $req->query(); // Parâmetros da Query
    }
}
```

Caso prefira, também é possível obter os parâmetros da URL através de uma variável explicita:

```php
$router->get('/products/{id}', [
    function($id, $request, $response) {
        return Pages\Product::getPage($id, $request, $response);
    }
]);

class Product extends Page {
    public static function getPage($id, $request, $response) {
        // ...
    }
}
```

### Parâmetros opcionais

Os parâmetros opcionais podem ser criados utilizando `?`:

```php
$router->get('/cart/{id?}', [
    function($request, $response) {
        // ...
    }
]);

$router->get('/product/{id}/{slug?}', [
    function($request, $response) {
        // ...
    }
]);
```

> Parâmetros opcionais não informados na requisição serão definidos como NULL.

### Rotas de erros

Alguns erros comuns podem ser tratados diretamente na definição da rota para personalizar a página de retorno:

```php
use \App\Controllers\Errors;

$router->error(404, [
    function($request, $response) {
        return Errors\PageNotFound::getPage($request, $response);
    }
]);
```

Também é possível definir uma rota padrão para qualquer erro:

```php
$router->error('default', [
    function($request, $response) {
        return Errors\General::getPage($request, $response);
    }
]);
```

### Rotas de direcionamento

Para realizar um redirecionamento em alguma rota, utilize a função `redirect()`:

```php
$router->get('/redirect', [
    function($request, $response) {
        return $request->getRouter()->redirect('/destination');
    }
]);
```

# Contribuindo com o projeto

Obrigado por considerar contribuir com o Luna! O guia de contribuição ainda encontra-se em desenvolvimento e em breve poderá entender como fazê-lo.

> Enquanto isso, você pode realizar contribuições por conta própria no repositório.

# Licença

O Luna é um software de código aberto sob a [MIT License](https://github.com/jjr-dev/luna/blob/main/LICENSE).

```

```
