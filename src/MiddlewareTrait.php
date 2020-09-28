<?php

namespace HnrAzevedo\Router;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use HnrAzevedo\Http\Response;

trait MiddlewareTrait{
    use Helper;

    protected static array $globalMiddlewares = [];

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return new Response();
    }

    public static function globalMiddlewares(array $middlewares): RouterInterface
    {
        foreach($middlewares as $middleware){
            if(!class_exists($middleware)){
                throw new \RuntimeException("Middleware class {$middleware} not exists");
            }
        }
        self::getInstance()->middlewares = $middlewares;
        return self::getInstance();
    }

    public static function middleware($middlewares): RouterInterface
    {
        $middlewares = (is_array($middlewares)) ? $middlewares : [ $middlewares ];
        $route = self::getInstance()->inSave();
        $route['middlewares'] = (is_array($route['middlewares'])) ? array_merge($route['middlewares'],$middlewares) : $middlewares;
        self::getInstance()->updateRoute($route,array_key_last(self::getInstance()->routes));
        return self::getInstance();
    }

    private static function existMiddleware(string $name): void
    {
        if(!class_exists($name) && !array_key_exists($name,self::$globalMiddlewares)){
            throw new \RuntimeException("Middleware {$name} does not exist");
        }
    }


}