<?php
namespace Luna\Http;

class Request {
    private $httpMethod;
    private $uri;
    private $router;
    private $pathParams = [];
    private $queryParams = [];
    private $body = [];
    private $headers = [];

    public function __construct($router) {
        $this->router       = $router;
        $this->httpMethod   = $_SERVER['REQUEST_METHOD'] ?? '';

        $this->setUri();
        $this->setBody();
        $this->setHeaders();
        $this->setQueryParams();
    }
    
    private function setUri() {
        $uri = $_SERVER['REQUEST_URI'] ?? '';
        $xUri = preg_split("/[?#]/", $uri);
        $this->uri = $xUri[0];
    }

    public function setHeaders($headers = null) {
        if (isset($headers) && $headers) {
            $this->headers = $headers;
            return;
        }
        
        $this->headers = getallheaders();
    }

    public function setQueryParams($queryParams = null) {
        if (isset($queryParams) && $queryParams) {
            $this->queryParams = $queryParams;
            return;
        }
        
        $this->queryParams = $_GET ?? [];
    }

    public function setBody($body = null) {
        if (isset($body) && $body) {
            $this->body = $body;
            return;
        }
        
        if($this->httpMethod == 'GET') return false;

        $this->body = $_POST ?? [];

        $inputRaw = file_get_contents('php://input');
        $this->body = (strlen($inputRaw) && empty($_POST)) ? json_decode($inputRaw, true) : $this->body;
    }

    public function addPathParams($key, $value) {
        $this->pathParams[$key] = empty($value) ? null : $value;
    }

    public function getRouter() {
        return $this->router;
    }

    public function getHttpMethod() {
        return $this->httpMethod;
    }

    public function getUri() {
        return $this->uri;
    }

    public function header($key = false) {
        if(!$key) return $this->headers;
        return isset($this->headers[$key]) ? $this->headers[$key] : null;
    }

    public function body($key = false) {
        if(!$key) return $this->body;
        return isset($this->body[$key]) ? $this->body[$key] : null;
    }

    public function query($key = false) {
        if(!$key)
            return $this->queryParams;

        if (!isset($this->queryParams[$key]))
            return null;

        $value = $this->queryParams[$key];
        
        if(is_string($value)) {
            $boolVerify = strtolower($value);
            
            if($boolVerify === 'true') $value = true;
            if($boolVerify === 'false') $value = false;
        }
        
        return $value;
    }

    public function param($key = false) {
        if(!$key) return $this->pathParams;
        return isset($this->pathParams[$key]) ? $this->pathParams[$key] : null;
    }
}