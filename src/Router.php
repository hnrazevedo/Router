<?php

namespace HnrAzevedo\Router;

use Exception;

class Router{
    use Helper, CheckTrait;

    private static $instance = null;
    private array $routers = [];
    private ?string $prefix = null;
    private $protocol = null;
    private $filtersSet = null;
    private $filter = null;
    private $group = false;
    private $lastReturn = null;

    public function __construct()
    {
        return $this;
    }

    public static function create(): Router
    {
        self::getInstance()->check_config();
        self::getInstance()->import(ROUTER_CONFIG['path']);
        return self::getInstance();
    }

    public static function getInstance(): Router
    {
        self::$instance = (is_null(self::$instance)) ? new self() : self::$instance;
        return self::$instance;
    }

    public static function form(string $uri, string $controll): Router
    {
        return self::getInstance()->add($uri, $controll, 'form');
    }

    public static function get(string $uri, string $controll): Router
    {
        return self::getInstance()->add($uri, $controll, 'get');
    }

    public static function post(string $uri, string $controll): Router
    {
        return self::getInstance()->add($uri, $controll, 'post');
    }

    public static function ajax(string $uri, string $controll): Router
    {
        return self::getInstance()->add($uri, $controll, 'ajax');
    }

    public static function add(string $uri, string $controll, string $protocol): Router
    {
        return self::getInstance()->set($uri, $controll, $protocol);
    }

    public function set($url , $role , $protocol = null): Router
    {
		$url = (substr($url,0,1) !=='/' and strlen($url) > 0) ? "/{$url}" : $url;

    	foreach($this->routers as $key => $value){
    		if( md5($this->prefix . $value['url'] . $value['protocol'] ) === md5( $url . $protocol ) ){
                throw new Exception("There is already a route with the url {$url} and with the {$protocol} protocol configured.");
            }
    	}

		$route = [
			'url' => $this->prefix.$url,
			'role' => $role,
			'protocol' => $protocol,
			'filters' => null,
            'group' => self::getInstance()->group
		];

		$this->routers[] = $route;		
        
        return self::getInstance();
    }

    public static function group(string $prefix,$callback): Router
    {
        self::getInstance()->prefix = (substr($prefix,0,1) !== '/') ? "/{$prefix}" : $prefix;
        self::getInstance()->group = sha1(date('d/m/Y h:m:i'));
        $callback();
        self::getInstance()->group = null;
        self::getInstance()->prefix = null;
        self::getInstance()->lastReturn = true;
        return self::getInstance();
    }

    public static function name(string $name): Router
    {

        if(self::getInstance()->lastReturn){
            throw new Exception("There is no reason to call a {$name} route group.");
        }

        $currentRoute = end(self::getInstance()->routers);

        foreach(self::getInstance()->routers as $key => $value){
            if(array_key_exists($name, self::getInstance()->routers)){
                throw new Exception("There is already a route with the name {$name} configured.");
            }
        }

        $currentRoute['name'] = $name;

        self::getInstance()->routers[count(self::getInstance()->routers)-1] = $currentRoute;

        self::getInstance()->lastReturn = null;

        return self::getInstance();
    }

    private function byName(?string $route_name)
    {
        if(!is_null($route_name)){
            $currentProtocol = $this->getProtocol();

            $this->check_name($route_name);
    
            $route = $this->routers[$route_name];
    
            $this->check_protocol($route['protocol'], $currentProtocol);
    
            $this->check_filtering($route);
    
            $this->Controller($route['role']);
            throw true;
        }
        
    }

    public function dispatch(?string $route_name = null): bool
    {
        $this->byName($route_name);

		$currentProtocol = $this->getProtocol();

        foreach(array_reverse($this->routers) as $r => $route){

            $this->hasProtocol($route, $currentProtocol);

            $route_loop = $this->explodeRoute( (substr($route['url'],strlen($route['url'])-1,1) === '/') , $route['url']);
            
            $route_request = $this->explodeRoute((substr($_SERVER['REQUEST_URI'],strlen($_SERVER['REQUEST_URI'])-1,1) === '/') , $_SERVER['REQUEST_URI']);

	        if($this->check_numparams($route_loop, $route_request) || $this->check_parameters($route_loop, $route_request)){
                continue;
            }
            
            $this->check_filtering($route);

            $this->Controller($route['role']);
	        return true;
	    }

	    throw new Exception('Page not found.',404);
    }

    public static function filter($filters): Router
    {
        if(self::getInstance()->lastReturn !== null){
            $currentGroup = end(self::getInstance()->routers)['group'];

            foreach (self::getInstance()->routers as $key => $value) {

                if($value['group'] === $currentGroup){
                    $currentRoute = self::getInstance()->addFilter(self::getInstance()->routers[$key],$filters);
                    self::getInstance()->routers[$key] = $currentRoute;
                }

            }
            
            return self::getInstance();
        }
        
        self::getInstance()->routers[count(self::getInstance()->routers)-1] = self::getInstance()->addFilter(end(self::getInstance()->routers),$filters);
        return self::getInstance();
    }

    public static function addFilter(array $route, $filter): array
    {
        if(is_null($route['filters'])){
            $route['filters'] = $filter;
            return $route;
        }

        $filters = (is_array($filter)) ? $filter : [0 => $filter];

        if(is_array($route['filters'])){
            foreach ($route['filters'] as $key => $value) {
                $filters[] = $value;
            }
        }else{
            $filters[] = $route['filters'];
        }

        $route['filters'] = $filters;
        return $route;
    }

}
