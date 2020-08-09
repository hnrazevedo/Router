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
        if(!class_exists('Controllers\\' . ucfirst(explode(':',$controll)[0]))){
            throw new Exception("No controller ".explode(':',$controll)[0]." found.");
        }
    }

    protected function check_controllmethod(string $controll)
    {
        if(!method_exists('Controllers\\' . ucfirst(explode(':',$controll)[0]), explode(':',$controll)[1])){
            throw new Exception("No method ".explode(':',$controll)[1]." found for ".explode(':',$controll)[0].".");
        }
    }

}