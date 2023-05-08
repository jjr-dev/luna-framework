<?php
    
    namespace App\Middlewares;

    use \App\Utils\Cache\File as CacheFile;

    class Cache {
        private function isCacheable($request) {
            if($request->getHttpMethod() != "GET") return false;

            if(getenv('ALLOW_NO_CACHE_HEADER') == 'true') {
                $headers = $request->getHeaders();

                if(isset($headers['Cache-Control']) && $headers['Cache-Control'] == 'no-cache') return false;
            }

            return true;
        }

        private function getHash($request) {
            $uri = $request->getRouter()->getUri();

            $queryParams = $request->getQueryParams();
            $uri .= !empty($queryParams) ? '?' . http_build_query($queryParams) : '';
            
            return rtrim('route-' . preg_replace('/[^0-9a-zA-Z]/', '-', ltrim($uri, '/')), '-');
        }

        public function handle($request, $response, $next) {
            if(!$this->isCacheable($request)) return $next($request, $response);

            $hash = $this->getHash($request);

            $cacheTime = $request->getRouter()->getCacheTime();

            return CacheFile::getCache($hash, $cacheTime, function() use($request, $response, $next) {
                return $next($request, $response);
            });
        }
    }