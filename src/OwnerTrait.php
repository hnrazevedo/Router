<?php

namespace HnrAzevedo\Router;

trait OwnerTrait
{
    protected bool $loaded = false;

    public function loadIn(string $name)
    {
        $this->hasRouteName($name);
        $this->loaded = true;
        return $this;
    }

    public function runIn(string $name)
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

    public function unsetRoute($key)
    {
        unset($this->routes[$key]);
        return $this;
    }

}