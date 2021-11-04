<?php

//session_start();

require __DIR__.'/../vendor/autoload.php';
require __DIR__.'/Routes/default.php';

use HnrAzevedo\Router\Router;

try{

    /**
     * Add to composer.json autoload
     */
    require __DIR__.'/Routes/Pipeline.php';

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

    die("Code Error: {$er->getCode()}".PHP_EOL."Line: {$er->getLine()}".PHP_EOL."File: {$er->getFile()}".PHP_EOL."Message: {$er->getMessage()}.");

}
