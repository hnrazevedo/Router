<?php

namespace HnrAzevedo\Router;

use Exception;

trait CheckTrait{

    protected function check_protocol(string $expected, string $current)
    {
        if($expected !== $current){
            throw new Exception('Page not found.',404);
        }
    }

    protected function check_name(string $route_name){
        if(!array_key_exists($route_name,$this->routers)){
            throw new Exception('Page not found.',404);
        }
    }

    protected function check_filtering(array $route)
    {
        $filters = (is_array($route['filters'])) ? $route['filters'] : [ $route['filters'] ];

        foreach($filters as $filter){
            $this->filter->filtering($filter);
        }
    }

}