<?php

namespace HnrAzevedo\Router;

trait Helper{
    
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

}