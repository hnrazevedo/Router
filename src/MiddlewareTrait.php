<?php

namespace HnrAzevedo\Router;

use Exception;

trait MiddlewareTrait{
    protected array $middlewares = [];

    protected function checkMiddleware(array $route)
    {
        $middlewares = (is_array($route['middlewares'])) ? $route['middlewares'] : [ $route['middlewares'] ];

        foreach($middlewares as $middleware){
            if(is_null($middleware)){
                continue;
            }

            $this->middlewareHandle($middleware);
        }

        return $this;
    }

    protected function middlewareHandle(string $m)
    {
        $middleware = $this->middlewareExists($m);
        $middleware->process($this->serverRequest, $this->request);
    }

    protected function middlewareExists(string $m)
    {
        if(class_exists(str_replace('::class','',$m))){
            $m = str_replace('::class','',$m);
            return new $m();
        }

        if(array_key_exists($m,$this->middlewares)){
            return new $this->middlewares[$m]();
        }

        throw new Exception("Middleware {$m} not found.");
    }

}
