<?php
    namespace App\Controllers\Pages;

    use \App\Utils\View;
    use \App\Utils\Seo;
    use \App\Utils\Page;
    use \App\Utils\Update;

    class Home extends Page {
        public static function homePage($req, $res) {
            Update::getReleases();

            $title = 'Luna - Framework MVC';

            $seo = new Seo();
            $seo->setTitle($title);
            $seo->setDescription('Framework MVC em PHP');
            $seo->setKeywords(['php', 'mvc', 'framework', 'luna']);
            $seo->setAuthor('jjr.dev');
            $seo->meta()->setType('website');
            $seo = $seo->render();

            $content = View::render('pages/home', [
                'version' => Update::getVersion()
            ]);
            
            $content = parent::getPage($title, $content, ['seo' => $seo]);

            return $res->send(200, $content);
        }
    }