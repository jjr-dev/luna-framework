<?php

namespace Luna\Http;

class Cors
{
    private static array $origins = ["*"];
    private static array $methods = ["*"];
    private static array $headers = ["*"];
    private static bool $credentials = true;
    private static int $maxAge = 0;

    public static function setOrigins(array $origins): void
    {
        self::$origins = $origins;
    }

    public static function getOrigins(): array
    {
        return self::$origins;
    }

    public static function setMethods(array $methods): void
    {
        self::$methods = $methods;
    }

    public static function getMethods(): array
    {
        return self::$methods;
    }

    public static function setHeaders(array $headers): void
    {
        self::$headers = $headers;
    }

    public static function getHeaders(): array
    {
        return self::$headers;
    }
    
    public static function setCredentials(bool $credentials): void
    {
        self::$credentials = $credentials;
    }

    public static function getCredentials(): bool
    {
        return self::$credentials;
    }

    public static function setMaxAge(int $maxAge): void
    {
        self::$maxAge = $maxAge;
    }

    public static function getMaxAge(): int
    {
        return self::$maxAge;
    }

    public static function addResponseHeaders(Response $response): void
    {
        $corsHeaders = [
            "Access-Control-Allow-Origin" => self::getOrigins(),
            "Access-Control-Allow-Methods" => self::getMethods(),
            "Access-Control-Allow-Headers" => self::getHeaders(),
            "Access-Control-Allow-Credentials" => self::getCredentials(),
            "Access-Control-Max-Age" => self::getMaxAge()
        ];

        $responseHeaders = $response->getHeaders();

        foreach ($corsHeaders as $header => $rule) {
            if (isset($responseHeaders[$header])) {
                continue;
            }
            
            $response->addHeader($header, $rule);
        }
    }
}