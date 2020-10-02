<?php

namespace HnrAzevedo\Router;

use Closure;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

interface RouterInterface extends MiddlewareInterface
{
    /**
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface;
    
    /**
     * @return self
     */
    public static function get(string $uri, $closure): RouterInterface;

    /**
     * @return self
     */
    public static function post(string $uri, $closure): RouterInterface;

    /**
     * @return self
     */
    public static function ajax(string $uri, $closure): RouterInterface;

    /**
     * @return self
     */
    public static function delete(string $uri, $closure): RouterInterface;

    /**
     * @return self
     */
    public static function put(string $uri, $closure): RouterInterface;

    /**
     * @return self
     */
    public static function patch(string $uri, $closure): RouterInterface;

    /**
     * @return self
     */
    public static function match(string $method, string $uri, $closure): RouterInterface;

    /**
     * @return self
     */
    public static function any(string $uri, $closure): RouterInterface;

    /**
     * @return self
     */
    public static function defineHost(string $host): RouterInterface;

    /**
     * @return self
     */
    public static function globalMiddlewares(array $middlewares): RouterInterface;
    
    /**
     * @return self
     */
    public static function middleware(array $middlewares): RouterInterface;

    /**
     * @return self
     */
    public static function name(string $name): RouterInterface;

    /**
     * @return self
     */
    public static function before($closure): RouterInterface;

    /**
     * @return self
     */
    public static function afterGroup($closure, ?array $excepts = null): RouterInterface;

    /**
     * @return self
     */
    public static function beforeGroup($closure, ?array $excepts = null): RouterInterface;

    /**
     * @return self
     */
    public static function after($closure): RouterInterface;

    /**
     * @return self
     */
    public static function beforeAll($closure, ?array $excepts = null): RouterInterface;

    /**
     * @return self
     */
    public static function afterAll($closure, ?array $excepts = null): RouterInterface;

    /**
     * @return self
     */
    public static function group(string $prefix, Closure $closure): RouterInterface;

    /**
     * @return self
     */
    public static function groupWhere(array $where, ?array $excepts = null): RouterInterface;

    /**
     * @return self
     */
    public static function groupMiddlewares(array $middlewares, ?array $excepts = null): RouterInterface;

    /**
     * @return self
     */
    public static function where(array $wheres): RouterInterface;

    /**
     * @return array
     */
    public static function current(): array;

    /**
     * @return string
     */
    public static function currentName(): string;

    /**
     * @return \Closure|string
     */
    public static function currentAction();

    /**
     * @return self
     */
    public static function load(?string $name = null): RouterInterface;

    /**
     * @return self
     */ 
    public static function run(?string $name = null): RouterInterface;
}
