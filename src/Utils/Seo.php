<?php
    namespace Luna\Utils;

    use Luna\Utils\Seo\Twitter;
    use Luna\Utils\Seo\Meta;
    use Luna\Utils\Environment as Env;

    class Seo {
        private $tags = [];
        private $twitter = false;
        private $meta = false;

        public function __construct($opts = []) {
            if(count($opts) == 0) {
                $default = Env::get('DEFAULT_SEO');
                $opts = explode(',', $default);
            }

            if(in_array('twitter', $opts)) $this->twitter();
            if(in_array('meta', $opts)) $this->meta();
        }
        
        public function setRobots($index = true, $follow = true) {
            $index = $index ? "index" : "noindex";
            $follow = $follow ? "follow" : "nofollow";

            $this->tags['robots'] = "{$index},{$follow}";
        }

        public function setTitle($title) {
            $this->tags['title'] = $title;

            if($this->twitter && !$this->twitter->hasTitle()) $this->twitter->setTitle($title);
            if($this->meta && !$this->meta->hasTitle()) $this->meta->setTitle($title);
        }

        public function setImage($image) {
            $this->tags['image'] = $image;

            if($this->twitter && !$this->twitter->hasImage()) $this->twitter->setImage($image);
            if($this->meta && !$this->meta->hasImage()) $this->meta->setImage($image);
        }

        public function setDescription($description) {
            $this->tags['description'] = $description;

            if($this->twitter && !$this->twitter->hasDescription()) $this->twitter->setDescription($description);
            if($this->meta && !$this->meta->hasDescription()) $this->meta->setDescription($description);
        }

        public function setKeywords($keys) {
            $this->tags['keywords'] = gettype($keys) == 'string' ? $keys : implode(', ', $keys);
        }

        public function setAuthor($author) {
            $this->tags['author'] = $author;
        }

        public function twitter($preConfigs = true) {
            if(!$this->twitter) {
                $this->twitter = new Twitter();

                if($preConfigs)
                    $this->setPreConfigs($this->twitter);
            };

            return $this->twitter;
        }

        public function meta($preConfigs = true) {
            if(!$this->meta) {
                $this->meta = new Meta();

                if($preConfigs)
                    $this->setPreConfigs($this->meta);
            };
            
            return $this->meta;
        }

        private function setPreConfigs($local) {
            if(isset($this->tags['title'])) $local->setTitle($this->tags['title']);
            if(isset($this->tags['description'])) $local->setDescription($this->tags['description']);
            if(isset($this->tags['image'])) $local->setImage($this->tags['image']);
        }

        public function render($withTitle = false) {
            $tags = "";

            if(isset($this->tags['title'])) {
                if($withTitle) $tags .= "<title>{$this->tags['title']}</title>\n";
                unset($this->tags['title']);
            }
            
            $tags .= $this->renderTags("name", "", $this->tags);

            if($this->twitter)
                $tags .= $this->renderTags("name", "twitter", $this->twitter->getTags());

            if($this->meta)
                $tags .= $this->renderTags("property", "og", $this->meta->getTags());

            return $tags;
        }

        private function renderTags($key, $prefix, $tags) {
            $template = "<meta {$key}='{{tag}}' content='{{content}}'>\n";

            $rendered = [];
            foreach($tags as $tag => $content) {
                if($prefix) $tag = $prefix . ":" . $tag;
                array_push($rendered, str_replace(['{{tag}}', '{{content}}'], [$tag, $content], $template));
            }
            
            return implode("", $rendered);
        }
    }