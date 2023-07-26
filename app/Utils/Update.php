<?php
    namespace App\Utils;

    class Update {
        private static $version = 'v1.0.4';
        private static $owner = 'jjr-dev';
        private static $repository = 'luna';
        private static $url = 'https://api.github.com';

        // https://chat.openai.com/share/1497c04e-f541-47f7-b0a3-610b74a25de7
        // https://api.github.com/repos/jjr-dev/luna/compare/v1.0.4...v1.0.5
        // TODO: Organizar releases por data ao invés de utilizar ordem do array
        // TODO: Verificar se é uma release ou pré-release (Receber parametro para atualizar para pré-release)

        public static function do() {
            $releases = self::getReleases();

            $last = $releases[0]['name'];
            $compare = self::getCompare(self::$version, $last);

            if(!$compare) return false;

            $dir = './';
            
            $files = $compare['files'];
            
            foreach($files as $file) {
                $filename = $file['filename'];
                $status = $file['status'];
                
                $content = false;
                if($status != 'removed')
                    $content = file_get_contents($file['raw_url']);

                $path = $dir . $filename;

                switch ($status) {
                    case 'added':
                        file_put_contents($path, $content);
                        break;
                    case 'removed':
                        unlink($path);
                        break;
                    case 'modified':
                        file_put_contents($path, $content);
                        break;
                    case 'renamed':
                        $previousFilename = $file['previous_filename'];
                        rename($dir . $previousFilename, $path);
                        file_put_contents($path, $content);
                        break;
                }
            }

            return true;
        }
        
        public static function has() {
            $releases = self::getReleases();

            $currentIndex = false;
            foreach($releases as $index => $release) {
                if($release['name'] == self::$version)
                    $currentIndex = $index;

                continue;
            }

            if($currentIndex === 0)
                return false;

            return true;
        }

        public static function getVersion() {
            return self::$version;
        }

        public static function getRepository() {
            return self::$repository;
        }

        public static function getOwner() {
            return self::$owner;
        }

        public static function getCompare($from, $to) {
            $url = self::$url . '/repos/' . self::$owner . '/' . self::$repository . '/compare/' . $from . '...' . $to;
            return self::request($url);
        }

        public static function getReleases() {
            $url = self::$url . '/repos/' . self::$owner . '/' . self::$repository . '/releases';
            return self::request($url);
        }

        private static function request($url) {
            $curl = curl_init();

            curl_setopt_array($curl, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => [
                    "Accept: application/vnd.github.v3+json",
                    "Content-Type: text/plain",
                    "User-Agent: Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/47.0.2526.111 YaBrowser/16.3.0.7146 Yowser/2.5 Safari/537.36"
                ]
            ]);

            $response = curl_exec($curl);
            $info = curl_getinfo($curl);

            curl_close($curl);

            if($info['http_code'] !== 200)
                return false;

            return json_decode($response, true);
        }
    }