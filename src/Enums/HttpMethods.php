<?php

namespace Luna\Enums;

enum HttpMethods: string
{
    case GET = "GET";
    case POST = "POST";
    case PUT = "PUT";
    case PATCH = "PATCH";
    case DELETE = "DELETE";
    case HEAD = "HEAD";
    case OPTIONS = "OPTIONS";

    public static function exists(string $method): bool
    {
        foreach (self::cases() as $case) {
            if ($case->value === strtoupper($method)) {
                return true;
            }
        }
        
        return false;
    }
}