<?php

namespace components;

class Router
{
    private mixed $routes;

    public function __construct()
    {
        // Читаєм і зпам'ятовуєм роути
        $routesPath = PROJECT_ROOT . '/config/routes.php'; // tmp
        $this->routes = include($routesPath); // tmp // змінній routes присвоюємо файл routes.php
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
        ) { // Порівнюєм строку запиту і дані які містяться в роутах
            $internalRoute = preg_replace("~$uri~", $route_path, $uri);

            $segments = explode('/', $internalRoute); // Визначаємо який котроллер і екшен обробляє запит
            $controllerName = array_shift(
                    $segments
                ) . 'Controller'; // array_shift отримує перший елемент з масива і видаляє його
            $controllerName = ucfirst($controllerName);
            $actionName = 'action' . ucfirst(array_shift($segments));
            $parameters = $segments;
            $controllerPath = '\src\controllers\\' . $controllerName;
            $controllerObject = new $controllerPath(
            ); // Створюєм об'єкт класу контроллер, передаєм змінну з ім'ям контроллера
            call_user_func_array(
                [$controllerObject, $actionName],
                $parameters
            ); // ? Для об'єкта викликаєм метод

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
        // Отримуємо строку запиту *.*/***
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
