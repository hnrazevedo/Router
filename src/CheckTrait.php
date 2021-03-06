<?php

declare(strict_types = 1);

namespace HnrAzevedo\Router;

trait CheckTrait
{
    use Helper;
    
    protected array $routesName = [];
    
    protected function hasRouteName(string $name): void
    {
        if(!isset($this->routesName[$name])){
            throw new \RuntimeException("There is no route named with {$name}");
        }
    }

    protected function isInNameGroup(): void
    {
        if(!is_null($this->getGroup())){
            throw new \RuntimeException("It is not allowed to assign names to groups");
        }
    }

    protected function isInPseudGroup(): void
    {
        if(!is_null($this->getGroup())){
            throw new \RuntimeException("To assign actions before or after the execution of the route, use beforeGroup or afterGroup");
        }
    }

    protected function existRouteName(string $name): void
    {
        if(isset($this->routesName[$name])){
            throw new \RuntimeException("There is already a route named with {$name}");
        }
    }

    protected function checkMethod(array $route, $method): void
    {
        $hasMethod = false;
        foreach(explode('|',$route['method']) as $routeMethod){
            if(@preg_match("/{$routeMethod}/",$method) !== 0 || $method === '*'){
                $hasMethod = true;
            }
        }
        if(!$hasMethod){
            throw new \Exception('This route is not released for the accessed method');
        } 
    }

    protected function throwCallable($value): void
    {
        if(is_callable($value)){
            throw new \Exception('Passing functions as attributes is not allowed');
        }
    }

}
