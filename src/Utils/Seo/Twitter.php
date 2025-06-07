<?php

namespace Luna\Utils\Seo;

use Luna\Utils\Seo;

class Twitter extends Seo
{
    private $tags = [];
    
    public function __construct() {}
    
    public function setTitle(?string $title): void
    {
        $this->tags['title'] = $title;
    }

    public function setCard($card)
    {
        $this->tags['card'] = $card;
    }
    
    public function setDescription(?string $description): void
    {
        $this->tags['description'] = $description;
    }

    public function setSite($site)
    {
        $this->tags['site'] = $site;
    }

    public function setImage(?string $image): void
    {
        $this->tags['image'] = $image;
    }

    public function setUrl($url)
    {
        $this->tags['url'] = $url;
    }

    public function getTags(): array
    {
        return $this->tags;
    }

    public function hasTitle(): bool
    {
        return isset($this->tags['title']);
    }

    public function hasDescription(): bool
    {
        return isset($this->tags['description']);
    }

    public function hasImage(): bool
    {
        return isset($this->tags['image']);
    }
}