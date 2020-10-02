<?php

namespace HnrAzevedo\Router\Example\Middleware;

use HnrAzevedo\Http\Response;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Middleware implements MiddlewareInterface{
    protected static array $data = [];
    protected static Response $response;

    public function __get($key)
    {
        return (array_key_exists($key,self::$data)) ? self::$data[$key] : null;
    }

    public function __set($key,$value)
    {
        self::$data[$key] = $value;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if(!isset(self::$response)){
            self::$response = new Response();
        }
        return self::$response;
    }

}