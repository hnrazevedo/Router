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

        if(!$filter->check(explode(':',$filtername)[1])){
            throw new Exception($filter->getMessage(explode(':',$filtername)[1]));
        }
    }

    protected function check_filClassExist(string $class)
    {
        if(class_exists(ROUTER_CONFIG['filter.namespace']."\\{$class}")){
            $filter = ROUTER_CONFIG['filter.namespace']."\\{$class}";
            return new $filter();
        }
        if(file_exists(ROUTER_CONFIG['path.filters'].$class.'.php')){
            require_once(ROUTER_CONFIG['path.filters'].$class.'.php');
            $filter = ROUTER_CONFIG['filter.namespace']."\\{$class}";
            return new $filter();
        }
        throw new Exception("Filter {$class} not found.");
    }

}
