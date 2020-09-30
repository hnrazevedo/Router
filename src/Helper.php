<?php

namespace HnrAzevedo\Router;

trait Helper
{
    protected array $routes = [];
    protected static Router $instance;
    protected string $host = '';

    public static function getInstance(): RouterInterface
    {
        self::$instance = (!isset(self::$instance)) ? new Router() : self::$instance;
        return self::$instance;
    }

    protected static function updateRoute(array $route, $key): RouterInterface
    {
        self::getInstance()->getRoutes()[$key] = $route;
        return self::getInstance();
    }

    public static function defineHost(string $host): Router
    {
        self::getInstance()->host = $host;
        return self::getInstance();
    }

    protected function inSave(): array
    {
        return end($this->routes);
    }

    protected function getHost(): string
    {
        return $this->host;
    }

    protected function getRoutes(): array
    {
        return $this->routes;
    }

    protected function setRoutes(array $routes): void
    {
        $this->routes =  $routes;
    }

}
