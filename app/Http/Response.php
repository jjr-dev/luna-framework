<?php

    namespace App\Http;

    class Response {
        private $headers = [];
        private $httpCode;
        private $contentType;
        private $content;

        public function send($httpCode, $content, $contentType = 'text/html') {
            $this->httpCode = $httpCode;
            $this->content  = $content;
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
            foreach($headers as $key => $value) {
                header($key . ': ' . $value);
            }
        }

        public function sendResponse() {
            $this->sendHeaders();
            
            switch($this->contentType) {
                case 'text/html':
                    echo $this->content;
                    exit;
                case 'application/json':
                    echo json_encode($this->content, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    exit;
            }
        }
    }