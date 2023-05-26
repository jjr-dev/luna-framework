<?php
    namespace App\Controllers\Pages;

    use \App\Utils\View;
    use \App\Utils\Seo;

    class Home extends Page {
        public static function homePage($req, $res) {
            $title = 'Luna - Framework MVC';

            $seo = new Seo();
            $seo->setTitle($title);
            $seo->setDescription('Framework MVC em PHP');
            $seo->setKeywords(['php', 'mvc', 'framework', 'luna']);
            $seo->setAuthor('jjr.dev');
            $seo->meta()->setType('website');
            $seo = $seo->render();

            $content = View::render('pages/home');
            $content = parent::getPage($title, $content, ['seo' => $seo]);

            return $res->send(200, $content);
        }
    }