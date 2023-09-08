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

# Sumário

-   [Aprendendo Luna (Documentação)](#aprendendo-luna)
    -   [Instalação](#instalação)
    -   [Inicializando](#inicializando)
    -   [Rotas](#rotas)
        -   [Métodos de rota disponíveis](#métodos-de-rota-disponíveis)
        -   [Parâmetros de rota](#parâmetros-de-rota)
        -   [Parâmetros opcionais](#parâmetros-opcionais)
        -   [Rotas de erros](#rotas-de-erros)
        -   [Rotas de redirecionamento](#rotas-de-redirecionamento)
    -   [Middlewares](#middlewares)
    -   [Cache](#cache)
    -   Controllers
    -   Services
    -   Helpers
    -   Views
    -   Flash Messages
    -   Components
    -   Pagination
    -   Database
    -   Models
    -   SEO
    -   Environment
-   [Contribuindo com o projeto](#contribuindo-com-o-projeto)
-   [Licença](#licença)

# Aprendendo Luna

## Instalação

Antes de iniciar seu projeto Luna, é necessário realizar a instalação do PHP (_versão 7.1 ou superior_) e [Composer](https://getcomposer.org/).

Utilize o comando `composer create-project phpluna/luna project-name` para instalar o Luna em seu projeto.

## Inicializando

Renomeie o arquivo `.env.example` para `.env` e configure a URL conforme necessário.

> As configurações gerais do projeto podem ser definidas no mesmo arquivo `.env`, como por exemplo a chave de autenticação de alguma API de terceiros.

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

## Middlewares

Os middlewares fornecem um mecanismo conveniente para validar requisições em rotas específicas:

```php
$router->get('/', [
    'middlewares' => [ 'maintenance' ],
    function($request, $response) {
        // ...
    }
]);
```

A classe do Middleware deve conter a função `handle` que será executada ao acessar a rota:

```php
namespace App\Middlewares;

class Maintenance {
    public function handle($request, $response, $next) {
        // ...

        return $next($request, $response);
    }
}
```

A função `handle` deve receber os parâmetros `$request`, `$response` e `$next` e deve retornar `$next($request, $response)` para prosseguir com a fila.

Após criar a classe do Middleware, é necessário defini-lo com um apelido para que seja utilizado na definição da rota:

```php
use \App\Http\Middleware;

Middleware::setMap([
    'maintenance' => \App\Middlewares\Maintenance::class
]);
```

É possível definir `middlewares padrões` que serão executados em todas as rotas criadas:

```php
Middleware::setDefault([
    'maintenance'
]);
```

## Cache

O armazenamento do retorno de rotas em cache reduz o tempo de retorno para futuras requisições da mesma rota:

```php
$router->get('/', [
    'cache' => 10000,
    function($request, $response) {
        // ...
    }
]);
```

> O tempo de cache é definido em milisegundos

As configurações de cache podem ser definidas no arquivo `.env`:

| Configuração          | Descrição                                   |
| --------------------- | ------------------------------------------- |
| CACHE_TIME            | Valor padrão de cache                       |
| CACHE_DIR             | Diretório de armazenamento do cache         |
| ALLOW_NO_CACHE_HEADER | Permitir o header `Cache-Control: no-cache` |

O valor de CACHE_TIME é definido como tempo de cache (também em milisegundos) quando o cache da rota for definido como true:

```php
$router->get('/', [
    'cache' => true,
    function($request, $response) {
        // ...
    }
]);
```

# Contribuindo com o projeto

Obrigado por considerar contribuir com o Luna! O guia de contribuição ainda encontra-se em desenvolvimento e em breve poderá entender como fazê-lo.

> Enquanto isso, você pode realizar contribuições por conta própria no repositório.

# Licença

O Luna é um software de código aberto sob a [MIT License](https://github.com/jjr-dev/luna-framework/blob/main/LICENSE).
