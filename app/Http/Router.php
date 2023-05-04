<?php

    namespace App\Http;
    use \Closure;
    use \Exception;
    use \ReflectionFunction;
    use \App\Http\Middleware\Queue AS MiddlewareQueue;

    class Router {
        private $url;
        private $prefix;
        private $routes = [];
        private $errors = [];
        private $request;

        public function __construct($url) {            
            $this->request = new Request();
            $this->url     = $url;
            $this->setPrefix();
        }

        private function setPrefix() {
            $parseUrl = parse_url($this->url);
            $this->prefix = $parseUrl['path'] ?? '';
        }

        private function toggleClosureToController($params) {
            foreach($params as $key => $value) {
                if($value instanceof Closure) {
                    $params['controller'] = $value;
                    unset($params[$key]);
                    continue;
                }
            }

            return $params;
        }

        private function addError($error, $params) {
            $params = $this->toggleClosureToController($params);

            $params['middlewares'] = $params['middlewares'] ?? [];
            $params['variables'] = [];

            $this->errors[$error] = $params;
        }

        private function addRoute($method, $route, $params) {
            $params = $this->toggleClosureToController($params);

            $params['middlewares'] = $params['middlewares'] ?? [];

            $params['variables'] = [];
            $patternVariable = '/{(.*?)}/';
            if(preg_match_all($patternVariable, $route, $matches)) {
                $route = preg_replace($patternVariable, '(.*?)', $route);
                $params['variables'] = $matches[1];
            }

            $patternRoute = '/^' . str_replace('/', '\/', $route) . '$/';
            $this->routes[$patternRoute][$method] = $params;
        }

        private function getUri() {
            $uri = $this->request->getUri();
            $xUri = strlen($this->prefix) ? explode($this->prefix, $uri) : [$uri];
            $uri = end($xUri);
            $uri = preg_split("/[?#]/", $uri);

            return rtrim($uri[0], '/');
        }

        private function getRoute() {
            $uri = $this->getUri();
            $httpMethod = $this->request->getHttpMethod();

            foreach($this->routes as $patternRoute => $methods) {
                if(preg_match($patternRoute, $uri, $matches)) {
                    if(isset($methods[$httpMethod])) {
                        unset($matches[0]);
                        $keys = $methods[$httpMethod]['variables'];
                        $methods[$httpMethod]['variables'] = array_combine($keys, $matches);
                        $methods[$httpMethod]['variables']['request'] = $this->request;

                        if(!isset($methods[$httpMethod]['controller']))
                            return $this->getError(500, 'URL ' . $uri . ' não pôde ser processada');

                        return $methods[$httpMethod];
                    }

                    return $this->getError(405, 'Método ' . $httpMethod . ' não permitido');
                }
            }

            return $this->getError(404, 'URL ' . $uri . ' não encontrada');
        }

        private function getError($errorCode, $msg) {
            if(!isset($this->errors[$errorCode]) && !isset($this->errors['default']))
                throw new Exception($msg, $errorCode);

            $error = $this->errors[isset($this->errors[$errorCode]) ? $errorCode : 'default'];        

            $error['variables']['request'] = $this->request;
            return $error;
        }

        public function run() {
            try {
                $route = $this->getRoute();

                $args = [];
                $reflection = new ReflectionFunction($route['controller']);
                foreach($reflection->getParameters() as $parameter) {
                    $name = $parameter->getName();
                    $args[$name] = $route['variables'][$name] ?? '';
                }

                $variables = $route['variables'];
                foreach($variables as $key => $value) {
                    if(!in_array($key, ['request']))
                        $this->request->addPathParams($key, $value);
                }

                return (new MiddlewareQueue($route['middlewares'], $route['controller'], $args))->next($this->request);
            } catch(Exception $e) {
                return new Response($e->getCode(), $e->getMessage());
            }
        }

        public function get($route, $params = []) {
            return $this->addRoute("GET", $route, $params);
        }

        public function post($route, $params = []) {
            return $this->addRoute("POST", $route, $params);
        }

        public function put($route, $params = []) {
            return $this->addRoute("PUT", $route, $params);
        }

        public function delete($route, $params = []) {
            return $this->addRoute("DELETE", $route, $params);
        }

        public function error($error, $params = []) {
            return $this->addError($error, $params);
        }
    }