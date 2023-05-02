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

Os controllers devem ser criados em `app/Controller/Pages`, por exemplo:

```php
<?php
    namespace App\Controller\Pages;

    use \App\Utils\View;
    use \App\Model\Entity\Organization;

    class Home extends Page {
        public static function getHome() {
            // Uso da model carregada
            $obOrganization = new Organization();

            // Renderização com variáveis
            $content = View::render('pages/home', [
                'name' => $obOrganization->name,
            ]);

            return parent::getPage("JJrDev - Home", $content);
        }
    }
```

## Criar model

As models devem ser criadas em `App/Model/Entity` com a conexão com o banco e regra de negócio, podendo ser utilizada pelo controller.

## Criar views

As páginas de views que serão executadas pelos controllers devem ser criadas em `resources/view/pages` em formato `.html`, o uso de variáveis é utilizado por `{{var}}` e deve ser enviado pelo controller conforme exemplo acima.

## Variáveis padrões

Para definir variáveis padrões em todas as views adicione-as no arquivo `index.php` em `View::init([])`
