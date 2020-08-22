<?php

namespace HnrAzevedo\Router;

trait Helper{
    use CheckTrait, ControllerTrait;
    
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
        $data = (!is_null($values)) ? json_decode($values['data'], true) : null;

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

    protected function explodeRoute(bool $bar, string $url): array
    {   
        return explode( '/', $bar ? substr($url, 0, -1) : $url );
    }

    protected function toHiking($walking)
    {
        if(is_string($walking)){
            $this->Controller($walking);
            return true;
        }

        call_user_func_array($walking,$this->getData()['GET']);
    }

}
