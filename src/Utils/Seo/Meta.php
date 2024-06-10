<?php
namespace Luna\Utils\Seo;

use Luna\Utils\Seo;

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