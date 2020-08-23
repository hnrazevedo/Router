<?php

namespace HnrAzevedo\Router;

use Exception;

trait CheckTrait{
    use FilterTrait, CheckWhere;

    protected function checkProtocol(string $expected, string $current): bool
    {
        return ($expected === $current);
    }

    protected function checkName(string $routeName){
        if(!array_key_exists($routeName,$this->routers)){
            throw new Exception('Page not found.', 404);
        }
    }

    protected function checkTypeRole($role){
        if(!is_string($role) && @get_class($role) !== 'Closure' ){
            throw new Exception("Improperly defined route track.");
        }
    }

    protected function checkConfig()
    {
        if(!defined('ROUTER_CONFIG')){
            throw new Exception("Information for loading routes has not been defined.");
        }
    }

    protected function checkNumparams(array $routeLoop, array $routeRequest)
    {
        return (count($routeLoop) !== count($routeRequest));
    }

    protected function checkParameters(array $routeLoop, array $routeRequest): bool
    {
        foreach($routeLoop as $rr => $param){
            if( (substr($param,0,1) === '{') ){
                $_GET[ substr($param,1,strlen($param)-2) ] = $routeRequest[$rr];    
            }
    
            if($this->checkParameter($param, $routeRequest[$rr])){
                return false;
            }
        }
        return true;
    }

    protected function checkParameter(string $routeLoop, string $routeRequest)
    {
        return !( substr($routeLoop,0,1) === '{' ) and $routeLoop !== $routeRequest;
    }

    protected function checkRole()
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

    protected function checkToHiking($route, $routeRequest, $routeLoop): bool
    {
        if($this->checkNumparams($routeLoop, $routeRequest) || 
            !$this->checkParameters($routeLoop, $routeRequest) ||
            !$this->checkWhere($route, $routeRequest)){
                return false;
        }
        return true;
    }

}
