<?php

namespace components;

class Router
{
    private mixed $routes;

    public function __construct()
    {
        $routesPath = PROJECT_ROOT . '/config/routes.php';
        $this->routes = include($routesPath);
    }

    public function run()
    {
        $success_run = false;
        $uri = $this->getURI();
        $method = $this->getMethod();
        
        $route_method = $this->routes[$uri][0] ?? null;
        $route_path = $this->routes[$uri][1] ?? null;

        if (
            isset($route_method) &&
            isset($route_path) &&
            $route_method === $method
        ) {
            $internalRoute = preg_replace("~$uri~", $route_path, $uri);

            $segments = explode('/', $internalRoute);
            $controllerName = array_shift(
                    $segments
                ) . 'Controller';
            $controllerName = ucfirst($controllerName);
            $actionName = 'action' . ucfirst(array_shift($segments));
            $parameters = $segments;
            $controllerPath = '\src\controllers\\' . $controllerName;
            $controllerObject = new $controllerPath(
            );
            call_user_func_array(
                [$controllerObject, $actionName],
                $parameters
            );

            $success_run = true;
        }

        if (!$success_run) {
            $view = new View();
            $view->render('/views/site/404');
        }
    }

    /**
     * @return string|false
     */
    private function getURI(): string|false
    {
        if (!empty($_SERVER['REQUEST_URI'])) {
            return trim($_SERVER['REQUEST_URI'], '/');
        } else {
            return '';
        }
    }

    /**
     * @return int
     */
    private function getMethod(): int
    {
        return match ($_SERVER['REQUEST_METHOD']) {
            'GET' => Helpers::GET,
            'POST' => Helpers::POST,
            default => Helpers::UNDEFINED,
        };
    }
}
