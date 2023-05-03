# ⚠️ EM DESENVOLVIMENTO

## Criar rota

Para criar uma nova rota de página acesse o arquivo `routes/page.php` e adicione como o exemplo:

```php
$obRouter->get('/about', [
    function() {
        return new Response(200, Pages\About::getAbout());
    }
]);
```

Rotas com variáveis devem ser como o exemplo:

```php
$obRouter->get('/pagina/{id}/{action}', [
    function($id, $action) {
        return new Response(200, 'Página ' . $id . ' - ' . $action);
    }
]);
```

## Criar controller

Os controllers devem ser criados em `app/Controllers/Pages`, por exemplo:

```php
<?php
    namespace App\Controllers\Pages;

    use \App\Utils\View;
    use \App\Models\Organization;

    class About extends Page {
        public static function getAbout() {
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

[Documentação do Eloquent](https://laravel-docs-pt-br.readthedocs.io/en/latest/eloquent/)

## Criar views

As páginas de views que serão executadas pelos controllers devem ser criadas em `resources/view/pages` em formato `.html`, o uso de variáveis é utilizado por `{{var}}` e deve ser enviado pelo controller conforme exemplo acima.

## Variáveis padrões

Para definir variáveis padrões em todas as views adicione-as no arquivo `index.php` em `View::init([])`
