<?php
    namespace App\Controllers\Pages;

    use \App\Utils\View;
    use \App\Models\Organization;

    class Home extends Page {
        public static function getHome($request) {
            $obOrganization = new Organization();
            
            $content = View::render('pages/home', [
                'name' => $obOrganization->name,
            ]);

            return parent::getPage("JJrDev - Home", $content);
        }
    }