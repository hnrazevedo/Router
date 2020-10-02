<?php

session_start();

require __DIR__.'/../vendor/autoload.php';
require __DIR__.'/Routes/default.php';

use HnrAzevedo\Router\Router;

try{

    Router::defineHost('https://localhost');
    
    Router::run();

    /**
     * @return array
     */ 
    $currentRoute = Router::current();

    /**
     * @return string
     */ 
    $name = Router::currentName();

    /**
     * @return Closure|string
     */ 
    $action = Router::currentAction();

}catch(Exception $er){

    die("Code Error: {$er->getCode()}<br>Line: {$er->getLine()}<br>File: {$er->getFile()}<br>Message: {$er->getMessage()}.");

}
