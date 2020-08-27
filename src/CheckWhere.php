<?php

namespace HnrAzevedo\Router;

use Exception;

trait CheckWhere{
        
    protected function checkWhereParam($data)
    {
        if(count($data) === 0){
            throw new Exception('It is necessary to define a condition to be tested.');
        }
    }

    protected function checkWhereParams($params)
    {
        if(count($params) === 0){
            throw new Exception('The route in question has no parameters to be tested.');
        }
    }

    protected function checkWhere($route, $request): bool
    {
        $pass = true;

        if(!is_array($route['where'])){
            return $pass;
        }

        $routeURI = explode('/',$route['url']);
        $params = [];
        foreach($routeURI as $p => $part){
            if(substr($part,0,1) === '{' && substr($part,-1) === '}'){
                $param = substr(str_replace('?','',$part),1,-1);

                if(array_key_exists($param,$route['where'])){
                    
                    $params[$param] = $route['where'][$param];

                    if(!preg_match("/^{$params[$param]}$/",$request[$p])){
                        $pass = false;
                    }
                }
                
            }
        }
        
        return $pass;
    }

    protected function callWhereAdd($data)
    {
        $this->checkWhereParam($data);
        
        $data = (count($data) > 1) ? [$data[0] => $data[1]] : $data[0];
        
        $route = end($this->routers);
        $routeURI = explode('/',$route['url']);
        $params = [];
        foreach($routeURI as $part){
            if(substr($part,0,1) === '{' && substr($part,-1) === '}'){
                $param = substr(str_replace('?','',$part),1,-1);

                if(array_key_exists($param,$data)){
                    $params[$param] = $data[$param];
                }
                
            }
        }

        $this->checkWhereParams($params);

        $route['where'] = (is_array($route['where'])) ? array_merge($route['where'],$params) : $params;

        $this->routers[count($this->routers)-1] = $route;
    }

}
