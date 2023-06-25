<?php
    namespace App\Utils;

    class Api {
        public static function success($data) {
            return ['data' => $data];
        }

        public static function error($error) {
            return ['error' => $error];
        }
    }