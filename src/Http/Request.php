<?php

namespace Luna\Http;

class Request
{
    private string $httpMethod;
    private string $uri;
    private object $router;
    private ?array $body = [];
    private ?array $headers = [];
    private ?array $pathParams = [];
    private ?array $queryParams = [];

    public function __construct(object $router)
    {
        $this->router = $router;
        $this->httpMethod = $_SERVER['REQUEST_METHOD'] ?? '';

        $this->setUri();
        $this->setBody();
        $this->setHeaders();
        $this->setQueryParams();
    }
    
    private function setUri(): void
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '';
        $xUri = preg_split("/[?#]/", $uri);
        $this->uri = $xUri[0];
    }

    public function setHeaders(?array $headers = null): void
    {
        if (isset($headers) && $headers) {
            $this->headers = $headers;
            return;
        }
        
        $this->headers = getallheaders();
    }

    public function setQueryParams(?array $queryParams = null): void
    {
        if (isset($queryParams) && $queryParams) {
            $this->queryParams = $queryParams;
            return;
        }
        
        $this->queryParams = $_GET ?? [];
    }

    public function setBody(?array $body = null): void
    {
        if (isset($body) && $body) {
            $this->body = $body;
            return;
        }
        
        if ($this->httpMethod === 'GET') {
            return;
        }

        $this->body = $_POST ?? [];

        $inputRaw = file_get_contents('php://input');
        $this->body = (strlen($inputRaw) && empty($_POST)) ? json_decode($inputRaw, true) : $this->body;
    }

    public function setPathParams(?array $pathParams): void
    {
        $this->pathParams = $pathParams;
    }

    public function addPathParams(string $key, $value): void
    {
        $this->pathParams[$key] = empty($value) ? null : $value;
    }

    public function getRouter(): object
    {
        return $this->router;
    }

    public function getHttpMethod(): string
    {
        return $this->httpMethod;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function header(?string $key = null)
    {
        if (!$key) {
            return $this->headers;
        }

        if (!isset($this->headers[$key])) {
            return null;
        }

        return $this->headers[$key];
    }

    public function body(?string $key = null)
    {
        if (!$key) {
            return $this->body;
        }

        if (!isset($this->body[$key])) {
            return null;
        }

        return $this->body[$key];
    }

    public function query(?string $key = null)
    {
        if (!$key) {
            return $this->queryParams;
        }

        if (!isset($this->queryParams[$key])) {
            return null;
        }

        $value = $this->queryParams[$key];
        
        if (is_string($value)) {
            $boolVerify = strtolower($value);
            
            if ($boolVerify === 'true') {
                $value = true;
            }
            
            if ($boolVerify === 'false') {
                $value = false;
            }
        }
        
        return $value;
    }

    public function param(?string $key = null)
    {
        if (!$key) {
            return $this->pathParams;
        }

        if (!isset($this->pathParams[$key])) {
            return null;
        }

        return $this->pathParams[$key];
    }
}