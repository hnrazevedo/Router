<?php

namespace HnrAzevedo\Router;

use Exception;

class Router{
    use Helper, CheckTrait, CheckWhere;

    private static $instance = null;
    private array $routers = [];
    private ?string $prefix = null;
    private $group = false;
    private $lastReturn = null;
    private bool $instanced = false;

    public function __construct()
    {
        return $this;
    }

    public static function create(): Router
    {
        if(!self::getInstance()->instanced){
            self::getInstance()->checkConfig();
            self::getInstance()->import(ROUTER_CONFIG['path']);
            self::getInstance()->instanced = true;
        }
        
        return self::getInstance();
    }

    public static function getInstance(): Router
    {
        self::$instance = (is_null(self::$instance)) ? new self() : self::$instance;
        return self::$instance;
    }

    public static function any(string $uri, $walking): Router
    {
        self::getInstance()->add($uri, $walking, 'get');
        self::getInstance()->add($uri, $walking, 'post');
        self::getInstance()->add($uri, $walking, 'form');
        return self::getInstance()->add($uri, $walking, 'ajax');
    }

    public static function get(string $uri, $walking): Router
    {
        return self::getInstance()->add($uri, $walking, 'get');
    }

    public static function post(string $uri, $walking): Router
    {
        return self::getInstance()->add($uri, $walking, 'post');
    }

    public static function ajax(string $uri, $walking): Router
    {
        return self::getInstance()->add($uri, $walking, 'ajax');
    }

    public static function form(string $uri, $walking): Router
    {
        return self::getInstance()->add($uri, $walking, 'form');
    }

    public static function add(string $uri, $walking, string $protocol): Router
    {
        return self::getInstance()->set($uri, $walking, $protocol);
    }

    public function set($url ,$walking , string $protocol): Router
    {
		$url = (substr($url,0,1) !=='/' and strlen($url) > 0) ? "/{$url}" : $url;

    	foreach($this->routers as $key => $value){
    		if( md5($this->prefix . $value['url'] . $value['protocol'] ) === md5( $url . $protocol ) ){
                throw new Exception("There is already a route with the url {$url} and with the {$protocol} protocol configured.");
            }
        }
        
        $this->checkTypeRole($walking);

		$route = [
			'url' => $this->prefix.$url,
			'role' => $walking,
			'protocol' => $protocol,
            'filters' => null,
            'where' => null,
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

    public static function where(): Router
    {
        if(self::getInstance()->lastReturn){
            throw new Exception("It is not possible to define parameter tests for groups of routes.");
        }

        $data = func_get_args();

        self::getInstance()->checkWhereParam($data);
        
        $data = (count($data) > 1) ? [$data[0] => $data[1]] : $data[0];
        
        $route = end(self::getInstance()->routers);
        $routeURI = explode('/',$route['url']);
        $params = [];
        foreach($routeURI as $part){
            if(substr($part,0,1) === '{' && substr($part,-1) === '}'){
                $param = substr($part,1,-1);

                self::getInstance()->checkExistParam($param,$data);

                $params[$param] = $data[$param];
            }
        }

        self::getInstance()->checkWhereParams($params);

        $route['where'] = (is_array($route['where'])) ? array_merge($route['where'],$params) : $params;

        self::getInstance()->routers[count(self::getInstance()->routers)-1] = $route;

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

    private function byName(?string $routName)
    {
        if(!is_null($routName)){
            $currentProtocol = $this->getProtocol();

            $this->checkName($routName);
    
            $route = $this->routers[$routName];
    
            if(!$this->checkProtocol($route['protocol'], $currentProtocol)){
                throw new Exception('Page not found.',404);
            }
    
            $this->checkFiltering($route);
    
            $this->toHiking($route['role']);
            throw true;
        }
    }

    public static function dispatch(?string $routeName = null): bool
    {
        $instance = self::create();

        $instance->getInstance()->byName($routeName);

		$currentProtocol = $instance->getInstance()->getProtocol();

        foreach(array_reverse($instance->getInstance()->routers) as $r => $route){

            $instance->getInstance()->currentRoute = $route;

            if(!$instance->getInstance()->checkProtocol($route['protocol'], $currentProtocol)){
                continue;
            }

            $instance->getInstance()->hasProtocol($route, $currentProtocol);

            $routeLoop = $instance->getInstance()->explodeRoute( (substr($route['url'],strlen($route['url'])-1,1) === '/') , $route['url']);
            
            $_SERVER['REQUEST_URI'] = (array_key_exists('REQUEST_URI', $_SERVER)) ? $_SERVER['REQUEST_URI'] : '';

            $routeRequest = $instance->getInstance()->explodeRoute((substr($_SERVER['REQUEST_URI'],strlen($_SERVER['REQUEST_URI'])-1,1) === '/') , $_SERVER['REQUEST_URI']);

	        if($instance->getInstance()->checkNumparams($routeLoop, $routeRequest) || !$instance->getInstance()->checkParameters($routeLoop, $routeRequest)){
                continue;
            }

            if($instance->getInstance()->checkWhere($route, $routeRequest)){

                $instance->getInstance()->checkFiltering($route);

                $instance->getInstance()->toHiking($route['role']);
                return true;
            }
            
        }
        
        $instance->getInstance()->currentRoute = null;

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

            self::getInstance()->lastReturn = null;
            
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
