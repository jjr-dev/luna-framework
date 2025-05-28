<?php
namespace Luna\Http;

class Request {
    private $httpMethod;
    private $uri;
    private $router;
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
    
    public function setBody($body) {
        $this->postVars = $body;
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
        if(!$key) return $this->postVars;
        return isset($this->postVars[$key]) ? $this->postVars[$key] : null;
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