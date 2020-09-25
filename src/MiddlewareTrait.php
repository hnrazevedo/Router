<?php

namespace HnrAzevedo\Router;

use Exception;
use Psr\Http\Server\MiddlewareInterface;
use HnrAzevedo\Http\Response;
use HnrAzevedo\Http\ServerRequest;

trait MiddlewareTrait{
    protected array $middlewares = [];

    protected function executeMiddleware(array $middlewares, ServerRequest $serverRequest, RequestHandler $request): Response
    {
        $response = new Response();
        foreach($middlewares as $middleware){
            if(is_null($middleware)){
                continue;
            }

            $response = $this->middlewareHandle($this->middlewareExists($middleware), $serverRequest, $request, $response);
        }

        return $response;
    }

    protected function middlewareHandle(MiddlewareInterface $middleware, ServerRequest $serverRequest, RequestHandler $request, Response $response): Response
    {
        return $middleware->process($serverRequest, $request);
    }

    protected function middlewareExists(string $middleware)
    {
        if(class_exists(str_replace('::class','',$middleware))){
            $middleware = str_replace('::class','',$middleware);
            return $this->getMiddleware($middleware);
        }

        if(array_key_exists($middleware,$this->middlewares)){
            return $this->getMiddleware($this->middlewares[$middleware]);
        }

        throw new Exception("Middleware {$middleware} not found.");
    }

    private function getMiddleware(string $class): MiddlewareInterface
    {
        return new $class();
    }

}
