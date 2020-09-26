<?php

namespace HnrAzevedo\Router;

use HnrAzevedo\Http\Uri;

trait DefinitionsTrait{
    use Helper;
    
    protected array $routes = [];
    
    public static function get(string $uri, $closure): Router
    {
        return self::set('get',$uri,$closure);
    }

    public static function post(string $uri, $closure): Router
    {
        return self::set('post',$uri,$closure);
    }

    public static function ajax(string $uri, $closure): Router
    {
        return self::set('ajax',$uri,$closure);
    }

    public static function delete(string $uri, $closure): Router
    {
        return self::set('delete',$uri,$closure);
    }

    public static function put(string $uri, $closure): Router
    {
        return self::set('put',$uri,$closure);
    }

    public static function patch(string $uri, $closure): Router
    {
        return self::set('patch',$uri,$closure);
    }

    public static function match(string $method, string $uri, $closure): Router
    {
        foreach(explode('|',$method) as $method){
            self::set($method, $uri, $closure);
        }
        return self::getInstance();
    }

    public static function any(string $uri, $closure): Router
    {
        return self::set('*',$uri,$closure);
    }

    private static function set(string $method, string $uri, $closure): Router
    {   
        $uri = (substr($uri,0,1) !=='/' and strlen($uri) > 0) ? "/{$uri}" : $uri;
        
        self::checkDuplicity($uri,$method);
        
		self::getInstance()->routers[] = [
			'uri' => new Uri(self::getInstance()->host.self::getInstance()->prefix.$uri),
			'action' => $closure,
			'method' => strtoupper($method),
            'middlewares' => null,
            'where' => null,
            'before' => null,
            'after' => null,
            'group' => self::getInstance()->group,
            'response' => null
        ];
        	
        return self::getInstance();
    }

    private static function checkDuplicity(string $uri, string $method): void
    {
        foreach(self::getInstance()->routers as $route){
    		if( md5($route['url'].$route['protocol']) === md5($uri.$method) ){
                throw new \RuntimeException("There is already a route with the URI {$uri} and with the {$method} METHOD configured.");
            }
        }
    }

}
