<?php

require __DIR__.'/../vendor/autoload.php';
require __DIR__.'/config.php';

use HnrAzevedo\Router\Router;

/* NOTE: in case of error an exception is thrown */

try{
    
    Router::create()->dispatch();

}catch(Exception $er){

    die($er->getCode().'  -  '.$er->getMessage());

}




