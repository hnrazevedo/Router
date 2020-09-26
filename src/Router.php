<?php

namespace HnrAzevedo\Router;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use HnrAzevedo\Http\Response;

class Router implements RouterInterface{
    use DefinitionsTrait, ExtraJobsTrait, CheckTrait;

    private array $currentRoute = [];
    private \Closure $beforeAll;
    private \Closure $afterAll;
    private string $host;
    private string $prefix = '';
    private bool $loaded = false;

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return new Response();
    }

    public static function defineHost(string $host): Router
    {
        self::getInstance()->host = $host;
        return self::getInstance();
    }

    public static function middleware(string $middleware): Router
    {
        return self::getInstance();
    }

    public static function name(string $name): Router
    {
        self::getInstance()->isInNameGroup();
        self::getInstance()->existRouteName($name);
        $route = self::getInstance()->inSave();
        $route['name'] = $name;
        self::getInstance()->routesNames[$name] = $name;
        self::getInstance()->unsetRoute(count(self::getInstance()->routes))->updateRoute($route,$name);
        return self::getInstance();
    }

    

    public static function group(string $prefix, \Closure $closure): Router
    {
        self::getInstance()->prefix = $prefix;
        self::getInstance()->group = sha1(date('d/m/Y h:m:i'));

        $closure();

        self::getInstance()->group = null;
        self::getInstance()->prefix = null;
        return self::getInstance();
    }

    public static function where(string $param, string $expression): Router
    {
        return self::getInstance();
    }

    public static function current(): array
    {
        self::getInstance()->hasCurrentRoute();
        return self::getInstance()->currentRoute;
    }

    public static function currentName(): string
    {
        self::getInstance()->hasCurrentRoute();
        return self::getInstance()->currentRoute['name'];
    }

    public static function currentAction()
    {
        self::getInstance()->hasCurrentRoute();
        return self::getInstance()->currentRoute['action'];
    }

    public static function load(): Router
    {
        self::getInstance()->loaded = true;
        return self::getInstance();
    }

    public static function run(): Router
    {
        if(!self::getInstance()->loaded){
            self::getInstance()->load();
        }
        // ...
        return self::getInstance();
    }

    // Extra functions
    

    public function loadIn(string $name): Router
    {
        $this->hasRouteName($name);
        $this->loaded = true;
        return $this;
    }

    public function runIn(string $name): Router
    {
        $this->hasRouteName($name);

        if(!$this->loaded){
            $this->loadIn($name);
        }
        
        return $this;
    }

    private function hasCurrentRoute(): void
    {
        if(!isset($this->currentRoute)){
            throw new \RuntimeException('Route not yet loaded');
        }
    }

    private function unsetRoute($key): Router
    {
        unset($this->routes[$key]);
        return $this;
    }

    private function inSave(): array
    {
        return end($this->routers);
    }
    
}