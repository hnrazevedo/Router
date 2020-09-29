<?php

namespace HnrAzevedo\Router;

trait OwnerTrait
{

    public function loadIn(string $name): RouterInterface
    {
        $this->hasRouteName($name);
        $this->loaded = true;
        return $this;
    }

    public function runIn(string $name): RouterInterface
    {
        $this->hasRouteName($name);

        if(!$this->loaded){
            $this->loadIn($name);
        }
        
        return $this;
    }

    public function hasCurrentRoute(): void
    {
        if(!isset($this->currentRoute)){
            throw new \RuntimeException('Route not yet loaded');
        }
    }

    public function unsetRoute($key): RouterInterface
    {
        unset($this->routes[$key]);
        return $this;
    }

}