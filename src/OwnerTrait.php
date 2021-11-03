<?php

declare(strict_types = 1);

namespace HnrAzevedo\Router;

trait OwnerTrait
{
    use Helper,
        CheckTrait;

    public function loadIn(string $name)
    {
        $this->hasRouteName($name);
        $this->loaded = true;
        return $this;
    }

    public function runIn(string $name)
    {
        $this->hasRouteName($name);

        if(!$this->loaded()) {
            $this->loadIn($name);
        }
        
        return $this;
    }

    public function hasCurrentRoute(): void
    {
        if(!isset($this->currentRoute)) {
            throw new \RuntimeException('Route not yet loaded');
        }
    }

    public function unsetRoute($key)
    {
        $routes = $this->getRoutes();
        unset($routes[$key]);
        $this->setRoutes($routes);
        return $this;
    }

}
