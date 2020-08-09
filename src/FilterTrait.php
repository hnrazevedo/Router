<?php

namespace HnrAzevedo\Router;

use Exception;

trait FilterTrait{
    protected function check_filtering(array $route)
    {
        $filters = (is_array($route['filters'])) ? $route['filters'] : [ $route['filters'] ];

        foreach($filters as $filter){
            if(is_null($filter)){
                continue;
            }
            $this->check_filter($filter);
        }
    }

    protected function check_filter(string $filtername)
    {
        if(count(explode(':',$filtername)) != 2){
            throw new Exception("Wrongly configured filter: {$filtername}.");
        }

        $filter = $this->check_filClassExist(explode(':',$filtername)[0]);
        $method = $this->check_filMethodExist($filter ,explode(':',$filtername)[1]);
        
        $filter->$method();
    }

    protected function check_filClassExist(string $class)
    {
        if(class_exists("Filter\\{$class}")){
            $filter = "Filter\\{$class}";
            return new $filter();
        }
        if(file_exists(ROUTER_CONFIG['path.filters'].$class.'.php')){
            require_once(ROUTER_CONFIG['path.filters'].$class.'.php');
            $filter = "Filter\\{$class}";
            return new $filter();
        }
        throw new Exception("Filter {$class} not found.");
    }

    protected function check_filMethodExist($filter, string $method): string
    {
        if(!method_exists($filter, $method)){
            $filter = get_class(($filter));
            throw new Exception("Filter {$method} not found in {$filter}.");
        }
        return $method;
    }

}