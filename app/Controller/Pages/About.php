<?php
    namespace App\Controller\Pages;

    use \App\Utils\View;
    use \App\Model\Entity\Organization;

    class About extends Page {
        public static function getAbout() {
            $obOrganization = new Organization();

            $content = View::render('pages/about', [
                'name' => $obOrganization->name,
                'description' => $obOrganization->description
            ]);

            return parent::getPage("JJrDev - Sobre", $content);
        }
    }