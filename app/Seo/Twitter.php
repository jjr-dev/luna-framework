<?php

    namespace App\Seo;

    class Twitter extends Seo {
        private $tags = [];
        
        public function __construct() {}
        
        public function setTitle($title) {
            $this->tags['title'] = $title;
        }

        public function setCard($card) {
            $this->tags['card'] = $card;
        }
        
        public function setDescription($description) {
            $this->tags['description'] = $description;
        }

        public function setSite($site) {
            $this->tags['site'] = $site;
        }

        public function setImage($image) {
            $this->tags['image'] = $image;
        }

        public function setUrl($url) {
            $this->tags['url'] = $url;
        }

        public function render() {
            return parent::renderTags("name", "twitter", $this->tags);
        }

        protected function hasTitle() {
            return isset($this->tags['title']);
        }

        protected function hasDescription() {
            return isset($this->tags['description']);
        }

        protected function hasImage() {
            return isset($this->tags['image']);
        }
    }