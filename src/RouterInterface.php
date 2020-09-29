<?php

namespace HnrAzevedo\Router;

use Closure;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

interface RouterInterface extends MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface;
    
    public static function get(string $uri, $closure): RouterInterface;

    public static function post(string $uri, $closure): RouterInterface;

    public static function ajax(string $uri, $closure): RouterInterface;

    public static function delete(string $uri, $closure): RouterInterface;

    public static function put(string $uri, $closure): RouterInterface;

    public static function patch(string $uri, $closure): RouterInterface;

    public static function match(string $method, string $uri, $closure): RouterInterface;

    public static function any(string $uri, $closure): RouterInterface;

    public static function defineHost(string $host): RouterInterface;

    public static function globalMiddlewares(array $middlewares): RouterInterface;
    
    public static function middleware($middlewares): RouterInterface;

    public static function name(string $name): RouterInterface;

    public static function before($closure): RouterInterface;

    public static function afterGroup($closure, $excepts): RouterInterface;

    public static function beforeGroup($closure, $excepts): RouterInterface;

    public static function after($closure): RouterInterface;

    public static function beforeAll($closure, $excepts): RouterInterface;

    public static function afterAll($closure, $excepts): RouterInterface;

    public static function group(string $prefix, Closure $closure): RouterInterface;

    public static function where(array $wheres): RouterInterface;

    public static function current(): array;

    public static function currentName(): string;

    public static function currentAction();

    public static function load(): RouterInterface;

    public static function run(): RouterInterface;
}
