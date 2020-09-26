<?php

namespace HnrAzevedo\Router;

trait CheckTrait{

    protected array $routers = [];
    protected array $routesNames = [];
    protected ?string $group = null;
    
    private function hasRouteName(string $name): void
    {
        if(!isset($this->routesName[$name])){
            throw new \RuntimeException("There is no route named with {$name}");
        }
    }

    private function isInNameGroup(): void
    {
        if(!is_null($this->group)){
            throw new \RuntimeException("It is not allowed to assign names to groups");
        }
    }

    private function isInPseudGroup(): void
    {
        if(!is_null($this->group)){
            throw new \RuntimeException("To assign actions before or after the execution of the route, use beforeGroup / afterGroup");
        }
    }

    private function existRouteName(string $name): void
    {
        if(isset($this->routesName[$name])){
            throw new \RuntimeException("There is already a route named with {$name}");
        }
    }

}
