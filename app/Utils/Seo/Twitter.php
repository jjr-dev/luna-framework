<?php
    namespace App\Utils\Seo;

    class Twitter {
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

        public function getTags() {
            return $this->tags;
        }

        public function hasTitle() {
            return isset($this->tags['title']);
        }

        public function hasDescription() {
            return isset($this->tags['description']);
        }

        public function hasImage() {
            return isset($this->tags['image']);
        }
    }