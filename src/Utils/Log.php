<?php
    namespace Luna\Utils;

    use Luna\Db\Log as LogModel;

    class Log {
        public static function save($code, $message) {
            $log = new LogModel();

            $publicId = uniqid();

            $log->public_id = $publicId;
            $log->code = $code;
            $log->message = $message;
            $log->save();

            return $publicId;
        }
    }