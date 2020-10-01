<?php

session_start();

echo '<pre>';

require __DIR__.'/../vendor/autoload.php';
require __DIR__.'/Routes/default.php';

use HnrAzevedo\Router\Router;

/* NOTE: in case of error an exception is thrown */

try{

    Router::globalMiddlewares([
        'Lasted'=> \HnrAzevedo\Router\Example\Middleware\Lasted::class
    ]);

    Router::defineHost('https://localhost');
    
    Router::run();

    /* Return current route */
    $currentRoute = Router::current();
    /* Return current name route*/
    $name = Router::currentName();
    /* Return current action route */
    $action = Router::currentAction();

}catch(Exception $er){

    die("Code Error: {$er->getCode()}, Line: {$er->getLine()}, File: {$er->getFile()}, Message: {$er->getMessage()}.");

}