<?php

namespace Luna\Http;

use Luna\Utils\Environment;

class Response
{
    private array $headers = [];
    private int $httpCode;
    private string $contentType;
    private string $content;

    public function send(int $httpCode, string|array|null $content = null, ?string $contentType = null)
    {
        $this->httpCode = $httpCode;
        $this->content = $content;

        if (!$contentType) {
            $defaultContentType = Environment::get('DEFAULT_CONTENT_TYPE');
            $contentType = $defaultContentType ?: 'text/html';
        }

        $this->setContentType($contentType);

        Cors::addResponseHeaders($this);

        return $this;
    }

    public function setContentType(string $contentType): void
    {
        $this->contentType = $contentType;
        $this->addHeader("Content-Type", $contentType);
    }

    public function addHeader(string $key, $value): void
    {
        $this->headers[$key] = is_array($value) ? implode(', ', $value) : $value;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    private function sendHeaders(): void
    {
        http_response_code($this->httpCode);
        
        foreach ($this->headers as $key => $value) {
            header($key . ': ' . $value);
        }
    }

    public function sendResponse(): void
    {
        $this->sendHeaders();
        
        if (in_array($this->contentType, ['html', 'text/html'])) {
            echo $this->content;
            exit;
        }
        
        if (in_array($this->contentType, ['json', 'application/json'])) {
            echo json_encode($this->content, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            exit;
        }
    }
}