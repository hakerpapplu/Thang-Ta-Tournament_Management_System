<?php

class Router {
    private static $routes = [];

    public static function get($uri, $action) {
        self::addRoute('GET', $uri, $action);
    }

    public static function post($uri, $action) {
        self::addRoute('POST', $uri, $action);
    }

    private static function addRoute($method, $uri, $action) {
        self::$routes[] = [
            'method' => $method,
            'uri' => trim($uri, '/'),
            'action' => $action
        ];
    }

    public static function dispatch() {
        $requestUri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        $requestMethod = $_SERVER['REQUEST_METHOD'];
    
        foreach (self::$routes as $route) {
            $routeUri = $route['uri'];
            $routeMethod = $route['method'];

            // Check if the method matches
            if ($routeMethod !== $requestMethod) {
                continue;
            }

            // Match static routes directly
            if ($routeUri === $requestUri) {
                self::executeAction($route['action']);
                return;
            }

            // Match dynamic routes (like /participants/edit/{id})
            $params = self::matchDynamicRoute($routeUri, $requestUri);
            if ($params !== false) {
                self::executeActionWithParams($route['action'], $params);
                return;
            }
        }

        http_response_code(404);
        echo "404 Not Found";
    }

    private static function matchDynamicRoute($routeUri, $requestUri) {
        $routeParts = explode('/', $routeUri);
        $requestParts = explode('/', $requestUri);

        // Check if the number of parts is the same
        if (count($routeParts) !== count($requestParts)) {
            return false;
        }

        $params = [];
        // Check each part of the route
        for ($i = 0; $i < count($routeParts); $i++) {
            if (isset($routeParts[$i]) && $routeParts[$i] !== $requestParts[$i] && $routeParts[$i] !== '{id}') {
                return false;
            }

            // If it's a dynamic part (e.g., {id}), capture the value
            if ($routeParts[$i] === '{id}') {
                $params['id'] = $requestParts[$i];
            }
        }

        // If we got here, the route is dynamic, and we have a match
        return $params;
    }

    private static function executeAction($action) {
        if (is_callable($action)) {
            call_user_func($action);
        } else {
            [$controllerName, $methodName] = explode('@', $action);
            $controllerClass = $controllerName;
            require_once "app/controllers/$controllerClass.php";
            $controller = new $controllerClass();
            $controller->$methodName();
        }
    }

    private static function executeActionWithParams($action, $params) {
        [$controllerName, $methodName] = explode('@', $action);
        $controllerClass = $controllerName;
        require_once "app/controllers/$controllerClass.php";
        $controller = new $controllerClass();

        // Pass the dynamic parameters to the controller method
        call_user_func_array([$controller, $methodName], $params);
    }
}
