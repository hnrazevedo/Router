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

    protected function checkExistParam($param, $data)
    {
        if(!array_key_exists($param,$data)){
            throw new Exception('Test parameter not available on the route in question.');
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
                $param = substr($part,1,-1);
                $params[$param] = $route['where'][$param];

                if(!preg_match("/^{$params[$param]}$/",$request[$p])){
                    $pass = false;
                }
            }
        }
        
        return $pass;
    }

}
