<?php

namespace HnrAzevedo\Router;

trait PrioritizeTrait{

    protected array $routes = [];

    protected function sortRoutes(): void
    {
        $staticRoutes = [];
        $paramRoutes = [];

        foreach($this->routes as $r => $route){

            $path = urldecode($route['uri']->getPath());

            if(strstr($path,'{')){
                $paramRoutes[$this->getKeyArray(substr_count($path,'/') + substr_count($path,'{'),$paramRoutes)] = $route;
                continue;    
            }

            $staticRoutes[$this->getKeyArray(substr_count($path,'/'),$staticRoutes)] = $route;

        }

        rsort($paramRoutes);
        rsort($staticRoutes);

        $this->orderRoutes(array_merge($staticRoutes,$paramRoutes));
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
                $kRoutes[$route['name']] = $route;
            }
        }
        ksort($kRoutes);
        $this->routes = $kRoutes;

    }
}
