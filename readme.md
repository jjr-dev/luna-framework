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
    -   [Controllers](#controllers)
        -   [Obtendo dados da requisição](#obtendo-dados-da-requisição)
        -   [Respondendo a requisição](#respondendo-a-requisição)
        -   [Tipos de resposta da requisição](#tipos-de-resposta-da-requisição)
    -   [Services](#services)
    -   [Helpers](#helpers)
    -   [Views](#views)
        -   [Padronização de página](#padronização-de-página)
        -   [Variáveis padrões](#variáveis-padrões)
    -   [Flash Messages](#flash-messages)
    -   [Components](#components)
    -   [Pagination](#pagination)
        -   [Template de paginação](#template-de-paginação)
    -   [Database](#database)
    -   [Models](#models)
    -   [SEO](#seo)
        -   [Twitter e Meta OpenGraph](#twitter-e-meta-opengraph)
        -   [Robots](#robots)
    -   [Environment](#environment)
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
        $id = $req->param('id');
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

## Controllers

As rotas executam (em sua maioria) Controllers:

```php
namespace App\Controllers;

class Product {
    public static function productPage($request, $response) {
        // ...
    }
}
```

### Obtendo dados da requisição

Os dados da requisição como heaaders, parâmetros query, body e outros podem ser obtidos através da variável `$request`:

```php
$request->header(); // Obter parâmetros do header
$request->query(); // Obter parâmetros da query
$request->body(); // Obter parâmetros do corpo
$request->param(); // Obter parâmetros da URL

$request->getUri(); // Obter URI
$request->getHttpMethod(); // Obter método HTTP
```

> É possível obter parâmetros específicos com as funções: `$request->query('id')`. Não especificar um parâmetro fará com que todos sejam retornados em array.

### Respondendo a requisição

Toda requisição deve ser respondida e sua resposta deve ser realizada no `return` da função do Controller:

```php
class Product {
    public static function productPage($request, $response) {
        return $response->send(200, "Sucesso");
    }
}
```

> Recomenda-se seguir o padrão de Status HTTP (_200 no exemplo_) listados [aqui](https://developer.mozilla.org/pt-BR/docs/Web/HTTP/Status).

### Tipos de resposta da requisição

As respostas da requisição podem retornar valores em `text/html`, `application/json` (mais comuns) ou outros (menos comuns):

```php
public static function getProduct($request, $response) {
    // ...
    return $response->send(200, ["data" => "Sucesso"], "application/json");
}
```

É possível também utilizar _alias_ para o retorno da requisição em HTML ou JSON:

```php
$res->send(200, $content, 'json'); // Ao invés de 'application/json'
$res->send(200, $content, 'html'); // Ao invés de 'text/html'
```

O tipo de resposta só deve ser informado na função caso o valor de `DEFAULT_CONTENT_TYPE` do arquivo `.env` seja diferente do desejado para o Controller.

## Services

Os Services auxiliam na obtenção e tratamento de dados entre o Banco de Dados e o Controller:

```php
namespace App\Services;

class Product {
    public static function find($id) {
        // ...
    }
}
```

Uso do service:

```php
use \App\Services\Product as ProductService;

class Product {
    public static function getProduct($request, $response) {
        $id = $request->param("id");
        $product = ProductService::find($id);
        return $response->send(200, $product);
    }
}
```

## Helpers

Um Helper agrupa pequenas funções úteis e que não são definidas como Services:

```php
namespace App\Helpers;

class Uuid {
    public function generate() {
        // ...
    }
}
```

Uso do Helper:

```php
use \App\Helpers\Uuid as UuidHelper;

class User {
    public function find($id) {
        return UuidHelper::generate();
    }
}
```

## Views

As views podem ser criadas em `resources/views` em `.html` e utilizadas na renderização:

```php
namespace App\Controllers\Pages;

use \App\Utils\View;

class Product {
    public static function productPage($request, $response) {
        $content = View::render('pages/product', [
            'name' => "Produto nome",
            'description' => "Produto descrição"
        ]);
        return $response->send(200, $content);
    }
}
```

Arquivo `resources/view/page/product.html`:

```html
<h1>{{name}}</h1>
<p>{{description}}</p>

<!-- Resultado: -->
<!-- <h1>Produto nome</h1> -->
<!-- <p>Produto descrição</p> -->
```

Para acessar diferentes níveis do `array`, utilize `->`, por exemplo:

```html
<p>{{name->first}}</p>
<p>{{phone->main->number}}</p>
```

Obteria os valores, de:

```php
$data = [
    'phone' => [
        'main' => [
            'number' => '12345'
        ]
    ],
    'name' => [
        'full' => 'Fulano de Tal',
        'first' => 'Fulano',
    ],
]
```

Variáveis que não são enviadas são, por padrão, removidas na renderização, caso deseje informar um valor caso a variável não seja informada, utilize `??`:

```html
<p>{{name ?? Nome não definido}}</p>
```

> As variáveis da View seguem as mesmas regras dos Components, sendo assim, ambos conseguem utilizar todos os recursos.

### Padronização de página

A classe `Page` possui funções que permitem padronizar as páginas com `header`, `footer` e outros itens padrões, alterando o valor de `content`:

```php
namespace App\Controllers\Pages;

use \App\Utils\View;

class Product extends Page {
    public static function productPage($request, $response) {
        $content = View::render('pages/product', [
            'name' => "Produto nome",
            'description' => "Produto descrição"
        ]);
        $content = parent::getPage("Produto Título", $content);

        return $response->send(200, $content);
    }
}
```

Com uso da classe `Page` a variável `$content` irá conter a junção dos arquivos `page.html`, `header.html` e `footer.html` (já existentes em `/resources/view`).

É possíve também adicionar novos arquivos padrões para cabeçalho e rodapé, podendo por exemplo criar diferentes cabeçalhos para a área pública e área administrativa:

```php
$content = parent::getPage("Produto Título", $content, [
    'header' => 'header-admin',
    'footer' => 'footer-admin'
]);
```

Para que os arquivos não sejam adicionadas, defina-o como `false`.

### Variáveis padrões

As variáveis mais comuns podem ser definidas no arquivo `index.html` em `View::define()` e podem ser utilizadas em qualquer View:

```html
<img src="{{PUBLIC}}/assets/img/php-logo.png" />

<a href="{{URL}}"><button>Início</button></a>
<a href="{{URL}}/products"><button>Produtos</button></a>
```

## Flash Messages

A Flash Message pode ser utilizada para retornar mensagens para a view de forma dinâmica:

```php
namespace App\Controllers\Pages;

use \App\Utils\Flash;

class Product {
    public static function productPage($request, $response) {
        // ...
        Flash::create("productNotFound", "Produto não encontrado", 'error');
    }
}
```

Após criar uma mensagem é possível renderiza-la para adicionar em uma view:

```php
Flash::create("productNotFound", "Produto não encontrado", 'error');
$flash = Flash::render("productNotFound");
```

É possível também renderizar uma mensagem que não tenha sido criada previamente:

```php
$flash = Flash::render(false, "Produto não encontrado", 'error');
```

> O armazenamento das mensagens é realizado na variavel de sessão `$_SESSION`, não cria-la previamente pode ser útil quando a mensagem não for utilizada em outros locais.

Caso deseje, renderize diversas mensagens de uma vez (apenas para mensagens criadas previamente):

```php
$flashs = Flash::renderAll(["productNotFound", "productOutOfStock"]);
```

Uma vez renderizada, adicione-a na view assim como outros parâmetros:

```php
$content = View::render('pages/product', ['flash' => $flash]);
```

> Certifique-se de adicionar o parâmetro {{flash}} ou correspondente na view que será utilizada.

O componente das mensagens flashs pode ser alterado em `/resources/components/flash/alert.html`.

Se necessário, é possível criar um componente no mesmo diretório e seleciona-lo na renderização:

```php
Flash::create("productNotFound", "Produto não encontrado", 'error', 'alert-new');
Flash::render("Produto não encontrado", 'error', 'alert-new');
```

O valor de `error` presente nos exemplos é aplicado na variável `{{type}}` do componente e pode ser personalizado com qualquer valor para estilização.

> Os tipos comuns são: `error`, `danger`, `warning`, `info`, `success`.

## Components

Pequenas estruturas de uma view que sejam repetidas (ou não) podem ser utilizadas como um componente:

```php
namespace App\Controllers\Pages;

use \App\Utils\Component;

class Product {
    public static function productsPage($request, $response) {
        // ...
        $productCard = Component::render('product-card', $product);
        $content = View::render('pages/product', ['productCard' => $productCard]);
    }
}
```

O componente deve ser criado em `.html` assim como a `view` no diretório `resources/components`.

> É possível também criar subpastas para organizar, por exemplo: `resources/components/product/card` e renderizar com `Component::render('product/card', $product)`.

### Renderização múltipla

Em situações onde o mesmo componente deve ser renderizado diversas vezes a partir de um `array`:

```php
$productCards = Component::multiRender('product-card', $products);
$content = View::render('pages/product', ['productCards' => $productCards]);
```

## Pagination

A paginação de `arrays` para listagem pode ser realizada com uso da classe `Pagination`:

```php
namespace App\Controllers\Pages;

use \App\Utils\Pagination;

class Product {
    public static function productsPage($request, $response) {
        // ...
        $pagination = new Pagination($products, $page, $limit);
        $products = $pagination->get();
    }
}
```

A função `get()` retornará a lista já paginada e outros dados sobre a paginação.

### Template de paginação

O controle da paginação pode ser renderizado para ser exibido na View:

```php
namespace App\Controllers\Pages;

use \App\Utils\Pagination;

class Product {
    public static function productsPage($request, $response) {
        // ...
        $pagination = new Pagination($products, $page, $limit);
        $paginationRender = $pagination->render($req);

        $content = View::render('pages/products', ['pagination' => $paginationRender]);
    }
}
```

Os componentes utilizados na criação da paginação podem ser modificados em `resources/components/pagination` e também podem ser alterados na renderização:

```php
$paginationRender = $pagination->render($req, [
    'last' => 'last.html'
    // ...
]);
```

> O `href` dos itens sempre utilizará o parâmetro {{page}} para definir a página destino.

Caso seja necessário remover algum item, defina o parâmetro como `false`.

Para limitar a quantidade de itens exibidos para cada lado do item atual, utilize:

```php
$paginationRender = $pagination->render($req, [], 3);
```

Exemplo de resultado da renderização com **3** itens para cada lado:

<center>

![Exemplo de paginação](https://i.imgur.com/0oXx93X.png)

</center>

> Estilização deve ser realizada separadamente

## Database

O acesso ao Banco de Dados de projetos Luna são realizados através do **Object-Relational Mapping** (ORM) utilizado no _Laravel_ chamado **Illuminate/Eloquent** e o mesmo permite o uso de funções simples e rápidas para escrever querys SQLs complexas.

A configuração das credênciais de acesso ao banco de dados deve ser realizada no arquivo `.env` e a conexão é estabelecida com:

```php
use Luna\Db\Database;
Database::boot();
```

> A conexão já é configurada no arquivo `index.html` do projeto.

Acesse a documentação completo do **Eloquent** [aqui](https://laravel.com/docs/5.0/eloquent).

## Models

O Model segue o padrão do ORM **Illuminate/Eloquent**:

```php
namespace App\Models;

use \App\Db\Database;
use \Illuminate\Database\Eloquent\Model;

class Product extends Model {
    // ...
}
```

Uso do model:

```php
namespace App\Services;

use \App\Models\Product;

class Product {
    public function find() {
        return Product::find(1);
    }

    public function list() {
        return Product::all();
    }
}
```

## SEO

O **Search Engine Optimization** (SEO) pode ser criado para exibição na View:

```php
namespace App\Controllers;

use \App\Utils\Seo;

class Product {
    public static function getProduct($request, $response) {
        // ...
        $seo = new Seo();
        $seo->setTitle("Produto nome");
        $seo->setDescription("Produto descrição");
        $seo->setKeywords(["produto-chave-1", "produto-chave-2"]);
        $seo->setImage("produto.png");
        $seo->setAuthor("Autor nome");
        $seoRender = $seo->render();

        $content = View::render('pages/product', ['seo' => $seoRender]);
    }
}
```

> A função `$seo->setKeywords()` pode receber as chaves em array ou em string, como por exemplo: `$seo->setKeywords("chave-1, chave-2")`.

> Caso não utilize um título definido separadamente na renderização da view em `parent::getPage` utilize `$seo->render(true)` para que a tag `<title>` seja renderizada pelo SEO.

### Twitter e Meta OpenGraph

A configuração para **Twitter** e **Meta OG** podem ser realizadas separadamente:

```php
$seo = new Seo();
$seo->setTitle("Produto nome");
$seo->twitter()->setTitle("Produto nome (Twitter)");
$seo->meta()->setTitle("Produto nome (Meta)");
```

É possível configurar todas as `tags` separadamente para cada rede:

```php
$seo->twitter()->setTitle($title);
$seo->twitter()->setDescription($description);
$seo->twitter()->setCard($card);
$seo->twitter()->setSite($site);
$seo->twitter()->setImage($image);
$seo->twitter()->setUrl($url);

$seo->meta()->setTitle($title);
$seo->meta()->setDescription($description);
$seo->meta()->setUrl($url);
$seo->meta()->setImage($image);
$seo->meta()->setType($type);
```

Caso os dados para **Twitter** e **Meta OG** sejam iguais, basta informar de uma das seguintes formas:

```php
$seo = new Seo();
$seo->twitter();
$seo->meta();
$seo->setTitle("Produto nome")
// ...
```

```php
$seo = new Seo(['twitter', 'meta']);
$seo->setTitle("Produto nome")
// ...
```

> Caso utilize `$seo->twitter()` ou `$seo->meta()` após o uso de `$seo->setTitle()` e outros, as definições de título, descrição e imagem serão compartilhadas, para desativar essa função utilize $seo->twitter(false) ou $seo->meta(false) no primeiro uso de cada.

Caso a classe `Seo` seja inicializada sem definir o **Twitter** ou **Meta OG** o valor definido no arquivo `.env` em `DEFAULT_SEO` será utilizado.

### Robots

A configuração de Robots podem ser adicionadas na renderização:

```php
$seo = new Seo();

$seo->setRobots($index, $follow);
```

As variáveis `$index` e `$follow` devem ser `Boolean`.

Exemplos de de definição do Robots:

```php
// Página indexada e com links seguidos:
$seo->setRobots();

// Página não indexada:
$seo->setRobots(false);

// Página com links não seguidos:
$seo->setRobots(true, false);

// Página não indexada e links não seguidos:
$seo->setRobots(false, false);
```

> Por padrão a indexação utiliza links seguidos, então se for utilizar a função `$seo->setRobots()` sem passar nenhum parâmetro, ela se torna dispensável.

## Environment

O arquivo `.env` pode ser utilizado para definir valores de configurações do projeto e podem ser obtidas em arquivos:

```php
use \App\Common\Environment;

Environment::get($key); // Obter item específico
Environment::get(); // Obter todos os items
```

É possível também armazenar valores dinamicamente que não estejam presentes no arquivo `.env`:

```php
Env::set($key, $value);
```

# Contribuindo com o projeto

Obrigado por considerar contribuir com o Luna! O guia de contribuição ainda encontra-se em desenvolvimento e em breve poderá entender como fazê-lo.

> Enquanto isso, você pode realizar contribuições por conta própria no repositório.

# Licença

O Luna é um software de código aberto sob a [MIT License](https://github.com/jjr-dev/luna-framework/blob/main/LICENSE).
