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
            if(is_null($filter)){
                continue;
            }
            $this->filter->filtering($filter);
        }
    }

    protected function check_config()
    {
        if(!defined('ROUTER_CONFIG')){
            throw new Exception("Information for loading routes has not been defined.");
        }
    }

    protected function check_numparams(array $route_loop, array $route_request)
    {
        return (count($route_loop) !== count($route_request));
    }

    protected function check_parameter(string $route_loop, string $route_request)
    {
        return !( substr($route_loop,0,1) === '{' ) and $route_loop !== $route_request;
    }

    protected function check_role()
    {
        if(!array_key_exists('role', $this->getData()['POST'])){
            throw new Exception('O servidor não conseguiu identificar a finalidade deste formulário.');
        }
    }

    protected function hasProtocol(array $route, string $currentProtocol)
    {
        $protocols = ( is_array($route['protocol']) ) ? $route['protocol'] : [ $route['protocol'] ];

        foreach($protocols as $protocol){
            if($protocol !== $currentProtocol){
                continue;
            }
        }
    }

}