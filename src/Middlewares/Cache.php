<?php

namespace Luna\Middlewares;

use Luna\Http\Request;
use Luna\Http\Response;
use Luna\Utils\Environment;
use Luna\Utils\Cache\File;

class Cache
{
    private function isCacheable(Request $request)
    {
        if ($request->getHttpMethod() !== "GET") {
            return false;
        }

        if (Environment::get('ALLOW_NO_CACHE_HEADER')) {
            $cacheControl = $request->header('Cache-Control');

            if ($cacheControl && $cacheControl === 'no-cache') {
                return false;
            }
        }

        return true;
    }

    private function getHash(Request $request)
    {
        $uri = $request->getRouter()->getUri();

        $queryParams = $request->query();
        $uri .= !empty($queryParams) ? '?' . http_build_query($queryParams) : '';
        
        return rtrim('route-' . preg_replace('/[^0-9a-zA-Z]/', '-', ltrim($uri, '/')), '-');
    }

    public function handle(Request $request, Response $response, callable $next)
    {
        if (!$this->isCacheable($request)) {
            return $next($request, $response);
        }

        $hash = $this->getHash($request);

        $cacheTime = $request->getRouter()->getCacheTime();

        return File::getCache($hash, $cacheTime, function() use($request, $response, $next) {
            return $next($request, $response);
        });
    }
}