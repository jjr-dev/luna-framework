# ⚠️ EM DESENVOLVIMENTO

## Criar rota

As rotas podem ser criadas no diretório `routes`, Por exemplo:

```php
<?php
    use \App\Http\Response;
    use \App\Controllers\Pages;

    $router->get('/about', [
        function($request) {
            return new Response(200, Pages\About::getAbout($request));
        }
    ]);
```

> A variável `$request` permite o uso de funções para obter os parâmetros e outros itens da requisição

Rotas com variáveis devem ser como o exemplo:

```php
$router->get('/pagina/{id}/{action}', [
    function($request, $id, $action) {
        return new Response(200, 'Página ' . $id . ' - ' . $action);
    }
]);
```

Também é possível obter os parâmetros de URL pela variável `$request` com a função `getPathParams()`, por exemplo:

```php
$router->get('/pagina/{id}/{action}', [
    function($request) {
        $pathParams = $request->getPathParams();
        return new Response(200, 'Página ' . $pathParams['id'] . ' - ' . $pathParams['action']);
    }
]);
```

### Rota de erro

As rotas podem ser definidas para personalizar o retorno de erros, por exemplo:
**Controller:**

```php
<?php
    namespace App\Controllers\Errors;

    use \App\Utils\View;
    use \App\Controllers\Pages\Page;

    class PageNotFound extends Page {
        public static function getPage($request) {
            $content = View::render('errors/404');

            return parent::getPage("Erro 404", $content);
        }
    }
```

**Rota:**

```php
use \App\Controllers\Errors;

$router->error(404, [
    function($request) {
        return new Response(404, Errors\PageNotFound::getPage($request));
    }
]);
```

É possível também definir uma rota `default`, ela será utilizada caso o erro não tenha uma rota específica:

```php
$router->error('default', [
    function($request) {
        return new Response(500, 'Erro geral');
    }
]);
```

> Caso a rota de erro não seja configurada irá retornar automaticamente o erro em STRING.
> Erros comuns: 404 -> Rota não encontrada | 405 -> Método não permitido

## Middlewares

Os middlewares devem ser criados em `app/Http/Middleware`, por exemplo:

```php
<?php
    namespace App\Http\Middleware;

    class Maintenance {
        public function handle($request, $next) {
            if(getenv('MAINTENANCE') == 'true')
                throw new \Exception("Página em manutenção.", 200);

            return $next($request);
        }
    }
```

A função `handle()` é obrigatória uma vez que será executada e deve retornar sempre com `$next($request)`.

Após criar o middleware é necessário lista-lo em `middlewares.php` para criar um apelido, por exemplo:

```php
<?php
    use \App\Http\Middleware\Queue AS MiddlewareQueue;

    MiddlewareQueue::setMap([
        'maintenance' => \App\Http\Middleware\Maintenance::class
    ]);

    MiddlewareQueue::setDefault([
        'maintenance'
    ]);
```

> Os middlewares definidos no array de `setDefault()` são executados em todas as rotas, para adicionar basta citar o apelido utilizado no `setMap()` conforme exemplo.

Exemplo de uso de middleware em rota:

```php
$router->get('/', [
    'middlewares' => [
        'middleware_name'
    ],
    function($request) {
        return new Response(200, Pages\Home::getHome($request));
    }
]);
```

## Criar controller

Os controllers devem ser criados em `app/Controllers`, por exemplo:

```php
<?php
    # Criado em app/Controllers/Pages

    namespace App\Controllers\Pages;

    use \App\Utils\View;
    use \App\Models\Organization;

    class About extends Page {
        public static function getAbout($request) {
            $organization = Organization::find(1);

            $content = View::render('pages/about', [
                'name' => $organization->name,
                'description' => $organization->description
            ]);

            return parent::getPage("JJrDev - Sobre", $content);
        }
    }
```

## Conexão com banco de dados

As credenciais de conexão ao banco de dados devem ser informadas no arquivo `.env`. A conexão com o banco é realizada com `PDO` com o **ORM** `Illuminate/Eloquent`.

## Criar model

As models devem ser criadas em `App/Models`, por exemplo:

```php
<?php

    namespace App\Models;

    use \App\Db\Database;
    use \Illuminate\Database\Eloquent\Model;

    class Organization extends Model {
        protected $table = 'tb_organization';
        protected $primaryKey = 'cd_organization';

        private $aliases = [
            'id'    => 'cd_organization',
            'name'  => 'nm_organization',
            'description' => 'ds_organization'
        ];

        public function __get($key) {
            return $this->getAttribute($this->aliases[$key] ?? $key);
        }
    }
```

É utilizado o **ORM** `Illuminate/Eloquent` para conexão, sendo assim as configurações do model seguem a documentação do mesmo.

> **Dicas caso não siga a convenção definida pelo Eloquent:**
> Defina a variável `$table` para nomear a tabela
> Defina a variável `$primaryKey` para definir a chave primaria da tabela
> Defina a variável `$aliases` para definir os alias das colunas
>
> - Isso auxilia para utilizar $tb->id ao invés de $tb->cd_column
> - A função `__get()` do exemplo é necessária caso utilize o `$aliases`.

### Exemplo de uso da model:

```php
<?php
    use \App\Models\Organization;

    class About extends Page {
        public static function getAbout() {
            $organization = Organization::find(1);
            var_dump($organization->id);
            var_dump($organization->name);
        }
    }
```

O tratamento de erros pode ser realizado com `try catch`, por exemplo:

```php
try {
    $organization = Organization::find(1);
} catch(\Illuminate\Database\QueryException $e) {
    var_dump($e->errorInfo);
}
```

[Documentação do Eloquent](https://laravel-docs-pt-br.readthedocs.io/en/latest/eloquent/)

## Criar views

As páginas de views que serão executadas pelos controllers devem ser criadas em `resources/view` em formato `.html`, o uso de variáveis é utilizado por `{{var}}` e deve ser enviado pelo controller conforme exemplo acima.

## Variáveis padrões

Para definir variáveis padrões em todas as views adicione-as no arquivo `index.php` em `View::init([])`
