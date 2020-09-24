<?php

namespace HnrAzevedo\Router;

use HnrAzevedo\Http\ServerRequest;

class Router{
    use Helper, DefinitionsTrait, ExtraJobsTrait;

    private static Router $instance;
    private ?string $group = null;
    private $beforeAll = null;
    private $afterAll = null;
    private array $afterExcepts = [];
    private array $beforeExcepts = [];
    private bool $instanced = false;
    private string $host = '';
    private RequestHandler $request;
    private ServerRequest $serverRequest;

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
        self::$instance = (!isset(self::$instance)) ? new self() : self::$instance;
        return self::$instance;
    }

    public static function host(string $host): Router
    {
        self::getInstance()->host = $host;
        return self::getInstance();
    }

    public function set(string $url ,$walking , string $protocol): Router
    {
        $this->lastReturn = null;
        
		$url = (substr($url,0,1) !=='/' and strlen($url) > 0) ? "/{$url}" : $url;

    	$this->checkExistence($url,$protocol);
        $this->checkTypeRole($walking);

		$route = [
			'url' => $this->prefix.$url,
			'role' => $walking,
			'protocol' => $protocol,
            'middlewares' => null,
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

    public static function group(string $prefix, callable $callback): Router
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
        return self::getInstance()->checkInGroup()->callWhereAdd(func_get_args());
    }

    public static function name(string $name): Router
    {
        self::getInstance()->checkInGroup()->hasRouteName($name);

        $currentRoute = end(self::getInstance()->routers);

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

    public static function load(?string $routeName = null): Router
    {
        return (!is_null($routeName)) ? self::create()->getInstance()->loadByName($routeName) : self::create()->getInstance()->loadByArray();
    }

    public static function dispatch(?string $routeName = null)
    {
        $instance = self::create()->getInstance();

        if(!$instance->loaded){
            self::load($routeName);
        }

        $instance->checkMiddleware($instance->currentRoute)->toHiking($instance->currentRoute);
    }

    public function middleware($middlewares): Router
    {
        if($this->lastReturn !== null){
            $currentGroup = end($this->routers)['group'];

            foreach ($this->routers as $key => $value) {
                if($value['group'] === $currentGroup){
                    $this->routers[$key] = $this->addMiddleware($this->routers[$key],$middlewares);
                }
            }
            
            return $this;
        }
        
        $this->routers[count($this->routers)-1] = $this->addMiddleware(end($this->routers),$middlewares);
        return $this;
    }

    public static function addMiddleware(array $route, $filter): array
    {
        if(is_null($route['middlewares'])){
            $route['middlewares'] = $filter;
            return $route;
        }

        $middlewares = (is_array($filter)) ? $filter : [0 => $filter];

        if(is_array($route['middlewares'])){
            foreach ($route['middlewares'] as $key => $value) {
                $middlewares[] = $value;
            }
        }else{
            $middlewares[] = $route['middlewares'];
        }

        $route['middlewares'] = $middlewares;
        return $route;
    }

    public static function defineMiddlewares(array $middlewares): Router
    {
        self::getInstance()->middlewares = $middlewares;
        return self::getInstance();
    }

}
