<?php

declare(strict_types = 1);

namespace HnrAzevedo\Router;

use HnrAzevedo\Http\Uri;
use Opis\Closure\SerializableClosure;

trait DefinitionsTrait
{
    use Helper;
    
    public static function get(string $uri, $closure): RouterInterface
    {
        return self::set('get',$uri,$closure);
    }

    public static function post(string $uri, $closure): RouterInterface
    {
        return self::set('post',$uri,$closure);
    }

    public static function ajax(string $uri, $closure): RouterInterface
    {
        return self::set('ajax',$uri,$closure);
    }

    public static function delete(string $uri, $closure): RouterInterface
    {
        return self::set('delete',$uri,$closure);
    }

    public static function put(string $uri, $closure): RouterInterface
    {
        return self::set('put',$uri,$closure);
    }

    public static function patch(string $uri, $closure): RouterInterface
    {
        return self::set('patch',$uri,$closure);
    }

    public static function match(string $method, string $uri, $closure): RouterInterface
    {
        self::set($method, $uri, $closure);
        return self::getInstance();
    }

    public static function any(string $uri, $closure): RouterInterface
    {
        return self::set('*',$uri,$closure);
    }

    private static $count = 0;

    private static function set(string $method, string $uri, $closure): RouterInterface
    {   
        $uri = (substr($uri,0,1) !=='/' and strlen($uri) > 0) ? "/{$uri}" : $uri;
        
        self::checkDuplicity($uri,$method);
        
        $routes = self::getInstance()->getRoutes();

        $index = sha1(date('d/m/Y H:i:s').count($routes));

        while(array_key_exists($index,$routes)){
            $index = sha1(date('d/m/Y H:i:s').rand(count($routes)));
        }

        $routes[$index] = [
			'uri' => serialize(new Uri(self::getInstance()->getHost().self::getInstance()->getPrefix().$uri)),
			'action' => (is_callable($closure)) ? serialize(new SerializableClosure($closure)) : serialize($closure),
			'method' => strtoupper($method),
            'middlewares' => [],
            'where' => [],
            'before' => [],
            'after' => [],
            'group' => self::getInstance()->getGroup(),
            'response' => null,
            'name' => $index
        ];

        self::getInstance()->setRoutes($routes);
        	
        return self::getInstance();
    }

    private static function checkDuplicity(string $uri, string $method): void
    {
        foreach(self::getInstance()->getRoutes() as $route){
    		if( md5($route['uri'].$route['method']) === md5($uri.$method) ){
                throw new \RuntimeException("There is already a route with the URI {$uri} and with the {$method} METHOD configured.");
            }
        }
    }

}
