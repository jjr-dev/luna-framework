<?php

namespace Luna\Utils;

use Luna\Utils\Seo\Twitter;
use Luna\Utils\Seo\Meta;
use Luna\Utils\Environment;

class Seo
{
    private array $tags = [];
    private $twitter;
    private $meta;

    public function __construct(array $opts = [])
    {
        if (count($opts) == 0) {
            $default = Environment::get('DEFAULT_SEO');
            $opts = explode(',', $default);
        }

        if (in_array('twitter', $opts)) {
            $this->twitter();
        }

        if (in_array('meta', $opts)) {
            $this->meta();
        }
    }
    
    public function setRobots(bool $index = true, bool $follow = true): void
    {
        $index = $index ? "index" : "noindex";
        $follow = $follow ? "follow" : "nofollow";

        $this->tags['robots'] = "{$index},{$follow}";
    }

    public function setTitle(?string $title): void
    {
        $this->tags['title'] = $title;

        if ($this->twitter && !$this->twitter->hasTitle()) {
            $this->twitter->setTitle($title);
        }

        if ($this->meta && !$this->meta->hasTitle()) {
            $this->meta->setTitle($title);
        }
    }

    public function setImage(?string $image): void
    {
        $this->tags['image'] = $image;

        if ($this->twitter && !$this->twitter->hasImage()) {
            $this->twitter->setImage($image);
        }

        if ($this->meta && !$this->meta->hasImage()) {
            $this->meta->setImage($image);
        }
    }

    public function setDescription(?string $description): void
    {
        $this->tags['description'] = $description;

        if ($this->twitter && !$this->twitter->hasDescription()) {
            $this->twitter->setDescription($description);
        }

        if ($this->meta && !$this->meta->hasDescription()) {
            $this->meta->setDescription($description);
        }
    }

    public function setKeywords(array|string|null $keys): void
    {
        $this->tags['keywords'] = is_array($keys) ? implode(', ', $keys) : $keys;
    }

    public function setAuthor(?string $author): void
    {
        $this->tags['author'] = $author;
    }

    public function twitter(bool $preConfigs = true): object
    {
        if (!$this->twitter) {
            $this->twitter = new Twitter();

            if ($preConfigs) {
                $this->setPreConfigs($this->twitter);
            }
        };

        return $this->twitter;
    }

    public function meta(bool $preConfigs = true): object
    {
        if (!$this->meta) {
            $this->meta = new Meta();

            if ($preConfigs) {
                $this->setPreConfigs($this->meta);
            }
        };
        
        return $this->meta;
    }

    private function setPreConfigs($local): void
    {
        if (isset($this->tags['title'])) {
            $local->setTitle($this->tags['title']);
        }

        if (isset($this->tags['description'])) {
            $local->setDescription($this->tags['description']);
        }

        if (isset($this->tags['image'])) {
            $local->setImage($this->tags['image']);
        }
    }

    public function render(bool $withTitle = false): string
    {
        $tags = "";

        if (isset($this->tags['title'])) {
            if ($withTitle) {
                $tags .= "<title>{$this->tags['title']}</title>\n";
            }
            
            unset($this->tags['title']);
        }
        
        $tags .= $this->renderTags("name", "", $this->tags);

        if ($this->twitter) {
            $tags .= $this->renderTags("name", "twitter", $this->twitter->getTags());
        }

        if ($this->meta) {
            $tags .= $this->renderTags("property", "og", $this->meta->getTags());
        }

        return $tags;
    }

    private function renderTags(string $key, string $prefix, array $tags): string
    {
        $template = "<meta {$key}='{{tag}}' content='{{content}}'>\n";

        $rendered = [];
        
        foreach ($tags as $tag => $content) {
            if ($prefix) {
                $tag = $prefix . ":" . $tag;
            }

            array_push($rendered, str_replace(['{{tag}}', '{{content}}'], [$tag, $content], $template));
        }
        
        return implode("", $rendered);
    }
}