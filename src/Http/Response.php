<?php
namespace Luna\Http;

use Luna\Utils\Environment as Env;

class Response {
    private $headers = [];
    private $httpCode;
    private $contentType;
    private $content;

    public function send($httpCode, $content, $contentType = false) {
        $this->httpCode = $httpCode;
        $this->content  = $content;

        if(!$contentType) {
            $defaultContentType = Env::get('DEFAULT_CONTENT_TYPE');
            $contentType = $defaultContentType ? $defaultContentType : 'text/html';
        }

        $this->setContentType($contentType);

        return $this;
    }

    public function setContentType($contentType) {
        $this->contentType = $contentType;
        $this->addHeader("Content-Type", $contentType);
    }

    public function addHeader($key, $value) {
        $this->headers[$key] = $value;
    }

    private function sendHeaders() {
        http_response_code($this->httpCode);
        foreach($this->headers as $key => $value) {
            header($key . ': ' . $value);
        }
    }

    public function sendResponse() {
        $this->sendHeaders();
        if(in_array($this->contentType, ['html', 'text/html'])) {
            echo $this->content;
            exit;
        } elseif(in_array($this->contentType, ['json', 'application/json'])) {
            echo json_encode($this->content, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            exit;
        }
    }
}