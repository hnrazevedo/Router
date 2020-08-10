<?php

namespace HnrAzevedo\Router;

use Exception;

trait ControllerTrait{

    protected function check_controllsettable(string $controll)
    {
        if(count(explode(':',$controll)) != 2){
            throw new Exception("Controller {$controll} badly set.");
        }
    }

    protected function check_controllexist(string $controll)
    {
        $controllname = ROUTER_CONFIG['controller.namespace'].'\\'.ucfirst(explode(':',$controll)[0]);
        if(!class_exists($controllname)){
            throw new Exception("No controller {$controllname} found.");
        }
    }

    protected function check_controllmethod(string $controll)
    {
        $controllname = ROUTER_CONFIG['controller.namespace'].'\\'.ucfirst(explode(':',$controll)[0]);
        $method = explode(':',$controll)[1];
        if(!method_exists($controllname, $method)){
            throw new Exception("No method {$method} found for {$controllname}.");
        }
    }

}