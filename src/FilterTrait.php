<?php

namespace HnrAzevedo\Router;

use Exception;

trait FilterTrait{
    protected function checkFiltering(array $route)
    {
        $filters = (is_array($route['filters'])) ? $route['filters'] : [ $route['filters'] ];

        foreach($filters as $filter){
            if(is_null($filter)){
                continue;
            }
            $this->checkFilter($filter);
        }
    }

    protected function checkFilter(string $filtername)
    {
        if(count(explode(':',$filtername)) != 2){
            throw new Exception("Wrongly configured filter: {$filtername}.");
        }

        $filter = $this->checkFilClassExist(explode(':',$filtername)[0]);

        if(!$filter->check(explode(':',$filtername)[1])){
            throw new Exception($filter->getMessage(explode(':',$filtername)[1]),403);
        }
    }

    protected function checkFilClassExist(string $class)
    {
        if(class_exists(ROUTER_CONFIG['filter.namespace']."\\{$class}")){
            $filter = ROUTER_CONFIG['filter.namespace']."\\{$class}";
            return new $filter();
        }
        throw new Exception("Filter {$class} not found.");
    }

}
