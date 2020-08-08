<?php

namespace HnrAzevedo\Router;

use HnrAzevedo\Validator\Validator;

use Exception;

class Router{
    use Helper;

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
        if(!defined('ROUTER_CONFIG')){
            throw new Exception("Information for loading routes has not been defined.");
        }
        
        self::import(ROUTER_CONFIG['path']);
        return self::getInstance();
    }

    public static function getInstance(): Router
    {
        if(is_null(self::$instance)){
            self::$instance = new self();
        }
        return self::$instance;
    }

    private static function import(string $path): Router
    {
        foreach (scandir($path) as $routeFile) {
            if(pathinfo($path.DIRECTORY_SEPARATOR.$routeFile, PATHINFO_EXTENSION) === 'php'){
                require_once($path. DIRECTORY_SEPARATOR .$routeFile);
            }
        }

        return self::getInstance();
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

    public function byName(string $route_name)
    {
        $currentProtocol = $this->getProtocol();

        if(!array_key_exists($route_name,$this->routers)){
            throw new Exception('Page not found.'.$route_name,404);
        }

        $route = $this->routers[$route_name];

        if($route['protocol']!==$currentProtocol){
            throw new Exception('Page not found.'.$route_name,404);
        }

        if(!empty($route['filters'])){
            if(is_array($route['filters'])){
                foreach($route['filters'] as $filter){
                    $this->filter->filtering($filter);
                }
            }else{
                $this->filter->filtering($route['filters']);
            }
        }

        $this->Controller($route['role']);
        return true;
    }

    public function dispatch(?string $route_name = null): bool
    {
        if(!is_null($route_name)){
            return $this->byName($route_name);
        }

		$currentProtocol = $this->getProtocol();

        foreach(array_reverse($this->routers) as $r => $route){
            if(is_array($route['protocol'])){
                foreach($route['protocol'] as $protocol){
                    if($protocol !== $currentProtocol){
                        continue;
                    }
                }
	        }else{
				if($route['protocol'] !== $currentProtocol){
                    continue;
                }
			}

	        $route_loop = explode(
                '/',
                (substr($route['url'],strlen($route['url'])-1,1) === '/') 
                    ? substr($route['url'], 0, -1) 
                    : $route['url'] 
            );

            /* ONLY FOR DEBUG CONDITION */
            $route_request = $route_loop;
	        /*$route_request = explode(
                '/',
                (substr($_SERVER['REQUEST_URI'],strlen($_SERVER['REQUEST_URI'])-1,1) === '/') 
                ? substr($_SERVER['REQUEST_URI'], 0, -1) 
                : $_SERVER['REQUEST_URI'] 
            );*/

	        if(count($route_loop) !== count($route_request)){
                continue;
            }

	        for($rr = 0; $rr < count($route_loop); $rr++){
	            $param = (substr($route_loop[$rr],0,1)==='{');

	            if($param){
                    $param_name = substr($route_loop[$rr],1,strlen($route_loop[$rr])-2);
	                $data[$param_name] = $route_request[$rr];
	            }

	            if(!$param and $route_loop[$rr] !== $route_request[$rr]){
                    continue 2;
                }
	        }

	        if(!empty($route['filters'])){
	            if(is_array($route['filters'])){
	                foreach($route['filters'] as $filter){
	                    $this->filter->filtering($filter);
	                }
	            }else{
	                $this->filter->filtering($route['filters']);
	            }
	        }

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
            
        }else{
            $currentRoute = self::getInstance()->addFilter(end(self::getInstance()->routers),$filters);
            self::getInstance()->routers[count(self::getInstance()->routers)-1] = $currentRoute;
        }

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

    







    public function Controller(string $controll): void
    {
        $data = $this->getData();

        foreach ($data['GET'] as $name => $value) {
            $controll = str_replace('{'.$name.'}',$value,$controll);
        }

        $d = explode(':',$controll);

        if(count($d) != 2){
            throw new Exception("Controller {$controll} badly set.");
        }

        if(!class_exists('Controllers\\' . ucfirst($d[0]))){
            throw new Exception("No controller {$d[0]} found.");
        }

        if(!method_exists('Controllers\\' . ucfirst($d[0]), $d[1])){
            throw new Exception("No method {$d[1]} found for {$d[0]}.");
        }

        $controller = 'Controllers\\' . ucfirst($d[0]);
        $controller = new $controller();
        $method = $d[1];

        $isForm = ( $this->getProtocol() == 'form');

        if($isform){
            $this->ControllerForm($controller, $method, $data['POST']);
        }else {
            $controller->$method($data);
        }
    }    

    public function ControllerForm($controller, string $method, array $values){
		if(Validator::execute($values)){
            if(!array_key_exists('role',$this->getData()['POST'])){
                throw new Exception('O servidor não conseguiu identificar a finalidade deste formulário.');
            }

            $role = ($method !== 'method') ? $method : $this->getData()['POST']['role'];
            $data = (!is_null($values)) ? json_decode($values['data']) : null;
            $controller->$role($data);
        }
    }

}
