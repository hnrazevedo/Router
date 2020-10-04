<?php

declare(strict_types = 1);

namespace HnrAzevedo\Router;

trait PrioritizeTrait
{
    use Helper;

    protected bool $sorted = false;

    protected function sorted(?bool $sorted = null): bool
    {
        if(null !== $sorted){
            $this->sorted = $sorted;
        }
        return $this->sorted;
    }
    
    protected function sortRoutes(): bool
    {
        if($this->sorted()){
            return true;
        }

        $staticRoutes = [];
        $paramRoutes = [];

        foreach($this->getRoutes() as $r => $route){

            $path = urldecode(unserialize($route['uri'])->getPath());

            if(strstr($path,'{')){
                $paramRoutes[$this->getKeyArray(substr_count($path,'/') + substr_count($path,'{'),$paramRoutes)] = $route;
                continue;    
            }

            $staticRoutes[$this->getKeyArray(substr_count($path,'/'),$staticRoutes)] = $route;
        }

        rsort($paramRoutes);
        rsort($staticRoutes);

        $this->orderRoutes(array_merge($staticRoutes,$paramRoutes));
        return $this->sorted(true);
    }

    private function getKeyArray(int $index, array $array): int
    {
        while(array_key_exists($index,$array)){
            $index++;
        }
        return $index;
    }

    private function orderRoutes(array $routes):void
    {
        $kRoutes = $routes;
        foreach($routes as $r => $route){
            if(array_key_exists('name',$route)){
                unset($kRoutes[$r]);
                $kRoutes["'{$route['name']}'"] = $route;
            }
        }
        ksort($kRoutes);
        $this->setRoutes($kRoutes);
    }
}
