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