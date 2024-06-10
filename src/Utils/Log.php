<?php
namespace Luna\Utils;

use Luna\Db\Log as LogModel;

class Log {
    public static function save($message, $code = null) {
        $log = new LogModel();

        $publicId = uniqid();

        $log->public_id = $publicId;
        $log->message = $message;
        $log->code = $code;
        $log->save();

        return $publicId;
    }
}