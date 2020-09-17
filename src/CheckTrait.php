<?php

namespace HnrAzevedo\Router;

use Exception;

trait CheckTrait{
    use FilterTrait, CheckWhere;

    protected function checkProtocol(string $expected, string $current): bool
    {
        return (strtoupper($expected) === strtoupper($current));
    }

    protected function checkName(string $routeName): Router
    {
        if(!array_key_exists($routeName,$this->routers)){
            throw new Exception('Page not found.', 404);
        }
        return $this;
    }

    protected function checkTypeRole($role): Router
    {
        if(!is_string($role) && @get_class($role) !== 'Closure' ){
            throw new Exception("Improperly defined route track.");
        }
        return $this;
    }

    protected function checkNumparams(array $routeLoop, array $routeRequest): bool
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

    protected function checkParameter(string $routeLoop, string $routeRequest): bool
    {
        return !( substr($routeLoop,0,1) === '{' ) and $routeLoop !== $routeRequest;
    }

    protected function checkRole(): Router
    {
        if(!array_key_exists('role', $this->getData()['POST'])){
            throw new Exception('O servidor não conseguiu identificar a finalidade deste formulário.');
        }
        return $this;
    }

    protected function hasProtocol(array $route, string $currentProtocol): Router
    {
        $protocols = ( is_array($route['protocol']) ) ? $route['protocol'] : [ $route['protocol'] ];

        foreach($protocols as $protocol){
            if(strtoupper($protocol) !== strtoupper($currentProtocol)){
                continue;
            }
        }

        return $this;
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

    protected function hasRouteName(string $name): Router
    {
        if(array_key_exists($name, $this->routers)){
            throw new Exception("There is already a route with the name {$name} configured.");
        }
        return $this;
    }

    protected function checkExistence(string $url, string $protocol): Router
    {
        foreach($this->routers as $key => $value){
    		if( md5($this->prefix . $value['url'] . $value['protocol'] ) === md5( $url . $protocol ) ){
                throw new Exception("There is already a route with the url {$url} and with the {$protocol} protocol configured.");
            }
        }
        return $this;
    }

    protected function checkInGroup(): Router
    {
        if($this->lastReturn){
            throw new Exception("At the moment it is not allowed to assign names or tests of parameters in groups..");
        }
        return $this;
    }

}
