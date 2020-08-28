<?php

namespace HnrAzevedo\Router;

trait Helper{
    use CheckTrait, ControllerTrait;
    
    private $currentRoute = null;

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

    protected function import(string $path)
    {
        foreach (scandir($path) as $routeFile) {
            if(pathinfo($path.DIRECTORY_SEPARATOR.$routeFile, PATHINFO_EXTENSION) === 'php'){
                require_once($path. DIRECTORY_SEPARATOR .$routeFile);
            }
        }
    }

    protected function ControllerForm($controller, string $method, array $values){
		$this->checkRole();
        $method = ($method !== 'method') ? $method : $this->getData()['POST']['role'];
        $data = (array_key_exists('data',$values)) ? json_decode($values['data'], true) : null;

        call_user_func_array([$controller,$method],  $data);
    }

    protected function Controller(string $controll): void
    {
        $data = $this->getData();

        foreach ($data['GET'] as $name => $value) {
            $controll = str_replace('{'.$name.'}',$value,$controll);
        }

        $this->checkControllsettable($controll);

        $this->checkControllexist($controll);

        $this->checkControllmethod($controll);

        $controller = ROUTER_CONFIG['controller.namespace'].'\\'. ucfirst(explode(':',$controll)[0]);
        $controller = new $controller();
        $method = explode(':',$controll)[1];

        if( ($this->getProtocol() == 'form') ){
            $this->ControllerForm($controller, $method, $data['POST']);
        }else {
            $data = (array_key_exists('data',$data['POST'])) ? json_decode($data['POST']['data'], true) : $data['GET'];
            call_user_func_array([$controller,$method],  $data);
        }
       
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

    protected function toHiking(array $route)
    {
        $this->callOnRoute($route,'before');

        if(is_string($route['role'])){
            $this->Controller($route['role']);
            $this->callOnRoute($route,'after');
            return true;
        }

        call_user_func_array($route['role'],$this->getData()['GET']);

        $this->callOnRoute($route,'after');
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
    }

}
