<?php

    namespace App\Utils\Seo;

    use \App\Utils\Seo;

    class Meta extends Seo {
        private $tags = [];

        public function __construct() {}
        
        public function setTitle($title) {
            $this->tags['title'] = $title;
        }

        public function setDescription($description) {
            $this->tags['description'] = $description;
        }

        public function setType($type) {
            $this->tags['type'] = $type;
        }

        public function setImage($image) {
            $this->tags['image'] = $image;
        }

        public function setUrl($url) {
            $this->tags['url'] = $url;
        }

        public function render() {
            return parent::renderTags("property", "og", $this->tags);
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