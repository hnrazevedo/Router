<?php

namespace HnrAzevedo\Router;

use Exception;

trait ControllerTrait{

    protected function checkControllsettable(string $controll)
    {
        if(count(explode(':',$controll)) != 2){
            throw new Exception("Controller {$controll} badly set.");
        }
    }

    protected function checkControllexist(string $controll)
    {
        $controllname = ucfirst(explode(':',$controll)[0]);
        if(!class_exists($controllname)){
            throw new Exception("No controller {$controllname} found.");
        }
    }

    protected function checkControllmethod(string $controll)
    {
        $controllname = ucfirst(explode(':',$controll)[0]);
        $method = explode(':',$controll)[1];
        if(!method_exists($controllname, $method)){
            throw new Exception("No method {$method} found for {$controllname}.");
        }
    }

}