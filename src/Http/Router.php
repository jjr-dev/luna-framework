<?php

namespace Luna\Http;

use Closure;
use Exception;
use Luna\Enums\HttpMethods;
use ReflectionFunction;
use Luna\Utils\Environment;

class Router
{
    private string $url;
    private string $prefix;
    private string $dir;
    private Request $request;
    private Response $response;
    private array $routes = [];
    private array $errors = [];
    private array $methods = [];

    public function __construct(string $url, ?string $dir = null)
    {  
        if (!$dir) {
            $dir = Environment::get("__DIR__") . '/routes';
        }

        $this->methods = HttpMethods::cases();

        $this->request = new Request($this);
        $this->response = new Response();
        $this->url = $url;
        $this->dir = $dir;
        $this->setPrefix();

        $this->load();
    }

    public function load()
    {
        $router = $this;
        
        $routerDir = dir($this->dir);

        while (($file = $routerDir->read()) !== false) {
            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            
            if ($ext === 'php') {
                include $this->dir . '/' . $file;
            }
        }

        $routerDir->close();

        foreach ($this->routes as $route) {
            $allowedMethods = array_keys($route);
            $originalRoute = $route[$allowedMethods[0]]['original_route'];

            if (in_array("OPTIONS", $allowedMethods)) {
                continue;
            }

            $this->addRoute("OPTIONS", $originalRoute, [
                function($_, $response) use ($allowedMethods) {
                    $response->addHeader("Access-Control-Allow-Methods", $allowedMethods);
                    return $response->send(200);
                }
            ]);
        }

        $router->run()->sendResponse();
    }

    private function setPrefix(): void
    {
        $parseUrl = parse_url($this->url);
        $this->prefix = $parseUrl['path'] ?? '';
    }

    private function toggleClosureToController(array $params): array
    {
        foreach ($params as $key => $value) {
            if ($value instanceof Closure) {
                $params['controller'] = $value;
                
                unset($params[$key]);
                continue;
            }
        }

        return $params;
    }

    private function addError(string $error, array $params): void
    {
        $params = $this->toggleClosureToController($params);

        $params['middlewares'] = $params['middlewares'] ?? [];
        $params['variables'] = [];

        $this->errors[$error] = $params;
    }

    private function addRoute(string $method, string $route, array $params): void
    {
        $params = $this->toggleClosureToController($params);
        
        $params['original_route'] = $route;

        $params['middlewares'] = $params['middlewares'] ?? [];
        $params['variables'] = [];

        $patterns = [
            '/{([^\/.]*)\?}/' => '?([^/.]*)?',
            '/{([^\/.]*)}/' => '([^/.]*)'
        ];
        
        foreach ($patterns as $pattern => $replace) {
            if (preg_match_all($pattern, $route, $matches)) {
                $route = preg_replace($pattern, $replace, $route);
                $params['variables'] = array_merge($matches[1], $params['variables']);
            }
        }

        $route = rtrim($route, '/');

        if (isset($params['cache']) && $params['cache']) {
            if (gettype($params['cache']) === 'boolean') {
                $params['cache'] = Environment::get('CACHE_TIME');
            }

            if (!in_array('cache', $params['middlewares'])) {
                $params['middlewares'][] = 'cache';
            }
        }

        $patternRoute = '/^' . str_replace('/', '\/', $route) . '$/';
        $this->routes[$patternRoute][$method] = $params;
    }

    public function getUri(): string
    {
        $uri = $this->request->getUri();
        $xUri = strlen($this->prefix) ? explode($this->prefix, $uri) : [$uri];
        $uri = end($xUri);

        return rtrim($uri, '/');
    }

    public function getCacheTime(): int|string
    {
        $route = $this->getRoute();
        return $route['cache'];
    }

    private function getRoute(): string|array
    {
        $uri = $this->getUri();
        $httpMethod = $this->request->getHttpMethod();

        foreach ($this->routes as $patternRoute => $methods) {
            if (preg_match($patternRoute, $uri, $matches)) {
                if (isset($methods[$httpMethod])) {
                    unset($matches[0]);
                    
                    $keys = $methods[$httpMethod]['variables'];
                    $methods[$httpMethod]['variables'] = array_combine($keys, $matches);
                    $methods[$httpMethod]['variables']['request'] = $this->request;
                    $methods[$httpMethod]['variables']['response'] = $this->response;

                    if (!isset($methods[$httpMethod]['controller'])) {
                        return $this->getError(500, 'URL ' . $uri . ' não pôde ser processada');
                    }

                    return $methods[$httpMethod];
                }

                return $this->getError(405, 'Método ' . $httpMethod . ' não permitido');
            }
        }

        return $this->getError(404, 'URL ' . $uri . ' não encontrada');
    }

    private function getError(int $code, string $message): array
    {
        if (!isset($this->errors[$code]) && !isset($this->errors['default'])) {
            throw new Exception($message, $code);
        }

        $error = $this->errors[isset($this->errors[$code]) ? $code : 'default'];        

        $error['variables']['request'] = $this->request;
        $error['variables']['response'] = $this->response;
        
        return $error;
    }

    public function run()
    {
        try {
            $route = $this->getRoute();

            $args = [];
            $reflection = new ReflectionFunction($route['controller']);
            
            foreach ($reflection->getParameters() as $parameter) {
                $name = $parameter->getName();

                if (isset($route['variables'][$name])) {
                    $value = $route['variables'][$name];

                    $args[$name] = !empty($value) ? $value : null;
                }
            }

            $variables = $route['variables'];
            
            foreach ($variables as $key => $value) {
                if (!in_array($key, ['request', 'response'])) {
                    $this->request->addPathParams($key, $value);
                }
            }

            return (
                new Middleware(
                    $route['middlewares'],
                    $route['controller'],
                    $args
                )
            )->next($this->request, $this->response);
        } catch(Exception $e) {
            return (new Response())->send($e->getCode(), $e->getMessage());
        }
    }

    public function redirect(string $route)
    {
        header('location: ' . $this->url . $route);
        exit;
    }

    public function get(string $route, array $params = []): void
    {
        $this->addRoute("GET", $route, $params);
    }

    public function post(string $route, array $params = []): void
    {
        $this->addRoute("POST", $route, $params);
    }

    public function put(string $route, array $params = []): void
    {
        $this->addRoute("PUT", $route, $params);
    }

    public function patch(string $route, array $params = []): void
    {
        $this->addRoute("PATCH", $route, $params);
    }

    public function options(string $route, array $params = []): void
    {
        $this->addRoute("OPTIONS", $route, $params);
    }

    public function delete(string $route, array $params = []): void
    {
        $this->addRoute("DELETE", $route, $params);
    }

    public function error(string $error, array $params = []): void
    {
        $this->addError($error, $params);
    }

    public function any(string $route, array $params = []): void
    {
        foreach ($this->methods as $method) {
            $this->addRoute($method->value, $route, $params);
        };
    }
    
    public function match(array $methods, string $route, array $params = []): void
    {
        foreach ($methods as $method) {
            $method = strtoupper($method);
            
            if (!HttpMethods::exists($method)) {
                continue;
            }

            $this->addRoute($method, $route, $params);
        };
    }
}