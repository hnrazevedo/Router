<?php

namespace HnrAzevedo\Router;

use Exception;

trait CheckTrait{
    use FilterTrait;

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

    protected function check_parameters(array $route_loop, array $route_request): bool
    {
        foreach($route_loop as $rr => $param){
            if( (substr($param,0,1) === '{') ){
                $_GET[ substr($param,1,strlen($param)-2) ] = $route_request[$rr];    
            }
    
            if($this->check_parameter($param, $route_request[$rr])){
                return false;
            }
        }
        return true;
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