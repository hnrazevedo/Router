<?php

namespace HnrAzevedo\Router;

trait Helper
{
    protected static Router $instance;

    public static function getInstance(): RouterInterface
    {
        self::$instance = (!isset(self::$instance)) ? new Router() : self::$instance;
        return self::$instance;
    }

    protected static function updateRoute(array $route, $key): RouterInterface
    {
        self::getInstance()->routes[$key] = $route;
        return self::getInstance();
    }

    protected function inSave(): array
    {
        return end($this->routes);
    }

}
