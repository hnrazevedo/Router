<?php

session_start();

require __DIR__.'/../vendor/autoload.php';
require __DIR__.'/config.php';

use HnrAzevedo\Router\Router;

/* NOTE: in case of error an exception is thrown */

try{
    
    Router::create()->dispatch();

}catch(Exception $er){

    die("Code Error: {$er->getCode()}, Line: {$er->getLine()}, File: {$er->getFile()}, Message: {$er->getMessage()}.");

}