<?php

namespace HnrAzevedo\Router;

use Exception;

class Router{
    use Helper, CheckTrait, CheckWhere, DefinitionsTrait, ExtraJobsTrait;

    private static $instance = null;
    private array $routers = [];
    private ?string $prefix = null;
    private ?string $group = null;
    private $lastReturn = null;
    private $beforeAll = null;
    private $afterAll = null;
    private array $afterExcepts = [];
    private array $beforeExcepts = [];
    private bool $instanced = false;
    private bool $loaded = false;

    public function __construct()
    {
        return $this;
    }

    public static function create(): Router
    {
        if(!self::getInstance()->instanced){
            self::getInstance()->instanced = true;
        }
        
        return self::getInstance();
    }

    public static function getInstance(): Router
    {
        self::$instance = (is_null(self::$instance)) ? new self() : self::$instance;
        return self::$instance;
    }

    public function set($url ,$walking , string $protocol): Router
    {
        $this->lastReturn = null;
        
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
            'before' => null,
            'beforeAll' => $this->beforeAll,
            'after' => null,
            'afterAll' => $this->afterAll,
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

        self::getInstance()->callWhereAdd(func_get_args());

        return self::getInstance();
    }

    public static function name(string $name): Router
    {
        if(self::getInstance()->lastReturn){
            throw new Exception("There is no reason to call a {$name} route group.");
        }

        $currentRoute = end(self::getInstance()->routers);

        self::getInstance()->hasRouteName($name);

        if(in_array($name,self::getInstance()->beforeExcepts)){
            $currentRoute['beforeAll'] = null;
        }

        if(in_array($name,self::getInstance()->afterExcepts)){
            $currentRoute['afterAll'] = null;
        }

        unset(self::getInstance()->routers[count(self::getInstance()->routers)-1]);
        
        self::getInstance()->routers[$name] = $currentRoute;

        return self::getInstance();
    }

    private function loadFromName(?string $routName = null): bool
    {
        if(!is_null($routName)){
            $currentProtocol = $this->getProtocol();

            $this->checkName($routName);
    
            $route = $this->routers[$routName];
    
            if(!$this->checkProtocol($route['protocol'], $currentProtocol)){
                throw new Exception('Page not found.',404);
            }

            $this->currentRoute = $route;
            $this->currentRoute['name'] = $routName;
            $this->loaded = true;

            return true;
        }
        return false;
    }

    public static function load(?string $routeName = null): Router
    {
        $instance = self::create()->getInstance();

        if($instance->loadFromName($routeName)){
            return $instance;
        }

		return $instance->loadFromArrays();
    }

    public static function dispatch(?string $routeName = null): bool
    {
        $instance = self::create()->getInstance();

        if(!$instance->loaded){
            self::load($routeName);
        }

        $instance->checkFiltering($instance->currentRoute);
        $instance->toHiking($instance->currentRoute);

        return true;
    }

    public function filter($filters): Router
    {
        if($this->lastReturn !== null){
            $currentGroup = end($this->routers)['group'];

            foreach ($this->routers as $key => $value) {

                if($value['group'] === $currentGroup){
                    $currentRoute = $this->addFilter($this->routers[$key],$filters);
                    $this->routers[$key] = $currentRoute;
                }

            }
            
            return $this;
        }
        
        $this->routers[count($this->routers)-1] = $this->addFilter(end($this->routers),$filters);
        return $this;
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
