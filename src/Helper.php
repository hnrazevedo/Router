<?php

namespace HnrAzevedo\Router;

trait Helper{
    use CheckTrait, ControllerTrait;
    
    public function getProtocol(): string
    {
        if((isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')){
            return 'ajax';
        }

        return strtolower($_SERVER['REQUEST_METHOD']);
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
		$this->check_role();
        $role = ($method !== 'method') ? $method : $this->getData()['POST']['role'];
        $data = (!is_null($values)) ? json_decode($values['data']) : null;
        $controller->$role($data);
    }

    protected function Controller(string $controll): void
    {
        $data = $this->getData();

        foreach ($data['GET'] as $name => $value) {
            $controll = str_replace('{'.$name.'}',$value,$controll);
        }

        $this->check_controllsettable($controll);

        $this->check_controllexist($controll);

        $this->check_controllmethod($controll);

        $controller = ROUTER_CONFIG['controller.namespace'].'\\'. ucfirst(explode(':',$controll)[0]);
        $controller = new $controller();
        $method = explode(':',$controll)[1];

        if( ( $this->getProtocol() == 'form') ){
            $this->ControllerForm($controller, $method, $data['POST']);
        }else {
            $controller->$method($data);
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
        $walking($this->getData()['GET']);
    }

}