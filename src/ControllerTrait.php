<?php

namespace HnrAzevedo\Router;

use Exception;

trait ControllerTrait{

    protected function checkControllSettable(string $controll)
    {
        if(count(explode(':',$controll)) != 2){
            throw new Exception("Controller {$controll} badly set.");
        }
        return $this;
    }

    protected function checkControllExist(string $controll)
    {
        $controllname = ucfirst(explode(':',$controll)[0]);
        if(!class_exists($controllname)){
            throw new Exception("No controller {$controllname} found.");
        }
        return $this;
    }

    protected function checkControllMethod(string $controll)
    {
        $controllname = ucfirst(explode(':',$controll)[0]);
        $method = explode(':',$controll)[1];
        if(!method_exists($controllname, $method)){
            throw new Exception("No method {$method} found for {$controllname}.");
        }
        return $this;
    }

}