<?php

    namespace App\Http;

    class Request {
        private $httpMethod;
        private $uri;
        private $pathParams = [];
        private $queryParams = [];
        private $postVars = [];
        private $headers = [];

        public function __construct($router) {
            $this->router       = $router;
            $this->queryParams  = $_GET ?? [];
            $this->headers      = getallheaders();
            $this->httpMethod   = $_SERVER['REQUEST_METHOD'] ?? '';

            $this->setUri();
            $this->setPostVars();
        }
        
        private function setUri() {
            $uri = $_SERVER['REQUEST_URI'] ?? '';
            $xUri = preg_split("/[?#]/", $uri);
            $this->uri = $xUri[0];
        }

        private function setPostVars() {
            if($this->httpMethod == 'GET') return false;

            $this->postVars = $_POST ?? [];

            $inputRaw = file_get_contents('php://input');
            $this->postVars = (strlen($inputRaw) && empty($_POST)) ? json_decode($inputRaw, true) : $this->postVars;
        }

        public function getHttpMethod() {
            return $this->httpMethod;
        }

        public function getUri() {
            return $this->uri;
        }

        public function getHeaders() {
            return $this->headers;
        }

        public function getQueryParams() {
            return $this->queryParams;
        }

        public function getPostVars() {
            return $this->postVars;
        }

        public function getPathParams() {
            return $this->pathParams;
        }

        public function getRouter() {
            return $this->router;
        }

        public function addPathParams($key, $value) {
            $this->pathParams[$key] = empty($value) ? null : $value;
        }
    }