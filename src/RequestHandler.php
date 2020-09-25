<?php

namespace HnrAzevedo\Router;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use HnrAzevedo\Http\Response;

class RequestHandler implements RequestHandlerInterface
{
    use MiddlewareTrait;

    protected array $routeMiddlewares = [];

    public function setMiddlewares(array $middlewares): RequestHandler
    {
        foreach($middlewares as $middleware){
            if(is_null($middleware)){
                continue;
            }
            $this->middlewareExists($middleware);
        }

        $this->routeMiddlewares = $middlewares;
        return $this;
    }

    public function setGlobalMiddlewares(array $middlewares): RequestHandler
    {
        $this->middlewares = $middlewares;
        return $this;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $clone = clone $this;
        return $this->executeMiddleware($this->routeMiddlewares, $request, $clone);
    }
}