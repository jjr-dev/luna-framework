<?php

namespace Luna\Utils;

class Log
{
    public static function save(string $message, string|int|null $code = null): string
    {
        $log = new \Luna\Db\Log();

        $publicId = uniqid();

        $log->public_id = $publicId;
        $log->message = $message;
        $log->code = $code;
        $log->save();

        return $publicId;
    }
}