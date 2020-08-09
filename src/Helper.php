<?php

namespace HnrAzevedo\Router;

use HnrAzevedo\Validator\Validator;

trait Helper{
    use CheckTrait, ControllerTrait;
    
    protected function getProtocol(): string
    {
        if((isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')){
            return 'ajax';
        }
        
        /* ONLY FOR DEBUG CONDITION */
        if(!array_key_exists('REQUEST_METHOD',$_SERVER)){
            return 'get';
        }

        return strtolower($_SERVER['REQUEST_METHOD']);
    }

    protected function getData(): ?array
    {
        return [
            'POST' => $_POST,
            'GET' => $_GET,
            'FILES' => $_FILES
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
		if(Validator::execute($values)){

            $this->check_role();

            $role = ($method !== 'method') ? $method : $this->getData()['POST']['role'];
            $data = (!is_null($values)) ? json_decode($values['data']) : null;
            $controller->$role($data);
        }
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

        $controller = 'Controllers\\' . ucfirst(explode(':',$controll)[0]);
        $controller = new $controller();
        $method = explode(':',$controll)[1];

        if( ( $this->getProtocol() == 'form') ){
            $this->ControllerForm($controller, $method, $data['POST']);
        }else {
            $controller->$method($data);
        }
    }    

    protected function explodeRoute(bool $bar, array $url): array
    {   
        return explode( '/', $bar ? substr($url, 0, -1) : $url );
    }

}