<?php
namespace Luna\Http;

class Cors {
    private static $origins = ["*"];
    private static $methods = ["*"];
    private static $headers = ["*"];
    private static $credentials = true;
    private static $maxAge = 0;

    public static function setOrigins($origins) {
        self::$origins = $origins;
    }

    public static function getOrigins() {
        return self::$origins;
    }

    public static function setMethods($methods) {
        self::$methods = $methods;
    }

    public static function getMethods() {
        return self::$methods;
    }

    public static function setHeaders($headers) {
        self::$headers = $headers;
    }

    public static function getHeaders() {
        return self::$headers;
    }
    
    public static function setCredentials($credentials) {
        self::$credentials = $credentials;
    }

    public static function getCredentials() {
        return self::$credentials;
    }

    public static function setMaxAge($maxAge) {
        self::$maxAge = $maxAge;
    }

    public static function getMaxAge() {
        return self::$maxAge;
    }

    public static function addResponseHeaders($response) {
        $corsHeaders = [
            "Access-Control-Allow-Origin" => self::getOrigins(),
            "Access-Control-Allow-Methods" => self::getMethods(),
            "Access-Control-Allow-Headers" => self::getHeaders(),
            "Access-Control-Allow-Credentials" => self::getCredentials(),
            "Access-Control-Max-Age" => self::getMaxAge()
        ];

        $responseHeaders = $response->getHeaders();

        foreach($corsHeaders as $header => $rule) {
            if (isset($responseHeaders[$header])) continue;
            
            $response->addHeader($header, $rule);
        }
    }
}