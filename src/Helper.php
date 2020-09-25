<?php

namespace HnrAzevedo\Router;


use Exception;

trait Helper{
    use CheckTrait, ControllerTrait;
    
    private $currentRoute = null;
    protected bool $loaded = false;
    protected $lastReturn = null;
    protected ?string $prefix = null;
    protected array $routers = [];

    public static function current(): ?array
    {
        return self::getInstance()->currentRoute;
    }

    public static function currentRouteName(): ?string
    {
        return self::getInstance()->currentRoute['name'];
    }

    public static function currentRouteAction()
    {
        return self::getInstance()->currentRoute['role'];
    }
    
    protected function getProtocol(): string
    {
        $protocol = ((isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')) ? 'ajax' : 'get';
        $protocol = (array_key_existS('HTTP_REQUESTED_METHOD',$_SERVER)) ? strtolower($_SERVER['HTTP_REQUESTED_METHOD']) : $protocol;
            
        return $protocol;
    }

    protected function getData(): ?array
    {
        return [
            'POST' => $_POST,
            'GET' => $_GET,
            'FILES' => $_FILES,
            'PROTOCOL' => $this->getProtocol()
        ];
    }

    protected function ControllerForm($controller, string $method, array $values)
    {
		$this->checkRole();
        $method = ($method !== 'method') ? $method : $this->getData()['POST']['role'];
        $data = (array_key_exists('data',$values)) ? json_decode($values['data'], true) : [];

        call_user_func_array([$controller,$method],  $data);
    }

    protected function Controller(string $controll)
    {
        $data = $this->getData();

        foreach ($data['GET'] as $name => $value) {
            $controll = str_replace('{'.$name.'}',$value,$controll);
        }

        $this->checkControllSettable($controll)->checkControllExist($controll)->checkControllMethod($controll);

        $controller = ucfirst(explode(':',$controll)[0]);
        $controller = new $controller();
        $method = explode(':',$controll)[1];

        if( ($this->getProtocol() == 'form') ){
            $this->ControllerForm($controller, $method, $data['POST']);
        }else {
            $data = (array_key_exists('data',$data['POST'])) ? json_decode($data['POST']['data'], true) : $data['GET'];
            call_user_func_array([$controller,$method],  $data);
        }

        return $this;
    }    

    protected function explodeRoutes(bool $bar, string $url ,bool $bar_, string $url_): array
    {   
        $url = $bar ? substr($url, 0, -1) : $url ;
        $url = explode('/',$url);

        $url_ = $bar_ ? substr($url_, 0, -1) : $url_ ;
        $url_ = explode('/',$url_);

        foreach($url as $ur => $u){
            if(substr($u,0,2) === '{?'){
                if(!array_key_exists($ur,$url_)){
                    $url_[$ur] = '';
                };
            }
        }

        return ['routeLoop' => $url, 'routeRequest' => $url_];
    }

    protected function run(array $route): bool
    {
        $this->callOnRoute($route,'beforeAll')->callOnRoute($route,'before');

        if(is_string($route['role'])){
            $this->Controller($route['role'])->callOnRoute($route,'after')->callOnRoute($route,'afterAll');
            return true;
        }

        call_user_func_array($route['role'],[$this->getData()['GET']]);

        $this->callOnRoute($route,'after')->callOnRoute($route,'afterAll');
        return true;
    }

    protected function callOnRoute(array $route,string $state)
    {
        if($route[$state] !== null){
            if(is_string($route[$state])){
                $this->Controller($route[$state]);
            }else{
                $route[$state]();
            }
        }
        return $this;
    }

    protected function loadByArray()
    {
        $currentProtocol = $this->getProtocol();

        foreach(array_reverse($this->routers) as $r => $route){

            $this->currentRoute = $route;
            $this->currentRoute['name'] = $r;

            if(!$this->checkProtocol($route['protocol'], $currentProtocol)){
                continue;
            }


            $this->hasProtocol($route, $currentProtocol);

            $_SERVER['REQUEST_URI'] = (array_key_exists('REQUEST_URI', $_SERVER)) ? $_SERVER['REQUEST_URI'] : '';

            $routs = $this->explodeRoutes(
                (substr($route['url'],strlen($route['url'])-1,1) === '/') , $route['url'],
                (substr($_SERVER['REQUEST_URI'],strlen($_SERVER['REQUEST_URI'])-1,1) === '/') , $_SERVER['REQUEST_URI']
            );

            if(!$this->checkToHiking($route, $routs['routeRequest'], $routs['routeLoop'])){
                continue;
            }         
            
            $this->loaded = true;
            return $this;
        }
        
        $this->currentRoute = null;
	    throw new Exception('Page not found.',404);
    }

    protected function loadByName(string $routName)
    {
        $currentProtocol = $this->getProtocol();
        $this->checkName($routName);
        $route = $this->routers[$routName];

        if(!$this->checkProtocol($route['protocol'], $currentProtocol)){
            throw new Exception('Page not found.',404);
        }

        $this->currentRoute = $route;
        $this->currentRoute['name'] = $routName;
        $this->loaded = true;

        return $this;            
    }

}
