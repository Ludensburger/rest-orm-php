<?php
class Router {
    private $request;
    private $routeMatcher;
    private $routes = [];

    public function __construct(RequestInterface $request, RouteMatcher $routeMatcher) {
        $this->request = $request;
        $this->routeMatcher = $routeMatcher;
    }

    public function addRoute($method, $path, $handler, $middleware = null) {
        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => $path,
            'handler' => $handler,
            'middleware' => $middleware
        ];
    }

    public function dispatch() {
        $match = $this->routeMatcher->match(
            $this->routes,
            $this->request->getMethod(),
            $this->request->getPath()
        );
    
        if ($match) {
            if (isset($match['middleware']) && $match['middleware'] !== null) {
                // Apply middleware
                $handler = call_user_func($match['middleware'], function($tokenData = null) use ($match) {
                    return call_user_func_array($match['handler'], array_values($match['params']));
                });
                return call_user_func($handler);
            } else {
                return call_user_func_array($match['handler'], array_values($match['params']));
            }
        }
    
        error_log("No route matched for " . $this->request->getMethod() . " " . $this->request->getPath());
        return new Response(404, json_encode(['error' => 'Not Found']));
    }
}