<?php

namespace HnrAzevedo\Router;

trait Helper
{
    protected array $routes = [];
    protected static Router $instance;
    protected string $host = '';
    private string $prefix = '';
    protected ?string $group = null;

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
        self::getInstance()->setHost($host);
        return self::getInstance();
    }

    protected function inSave(): array
    {
        return end($this->routes);
    }

    protected function setHost(string $host): void
    {
        $this->host = $host;
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

    protected function getGroup(): ?string
    {
        return $this->group;
    }

    protected function setGroup(?string $group): void
    {
        $this->group = $group;
    }

    protected function getPrefix(): ?string
    {
        return $this->prefix;
    }

    protected function setPrefix(string $prefix): void
    {
        $this->prefix = $prefix;
    }
}
