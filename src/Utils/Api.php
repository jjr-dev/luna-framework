<?php
    namespace App\Utils;

    class Api {
        public static function success($data, $code = 200) {
            return ['data' => $data, 'code' => $code];
        }

        public static function error($error, $code = 400) {
            return ['error' => $error, 'code' => $code];
        }
    }