<?php

use HnrAzevedo\Router\Router;

/* Returning parameters passed via URL in anonymous functions */
Router::get('/{parameter}/{otherparameter}', function($parameter, $otherparameter){
    echo "Parameter 1:{$parameter}, Parameter 2:{$otherparameter}.";
});

/* Passing controller and/or method via parameter in URL */
Router::get('/{controller}/{method}','{controller}:{method}');
//Router::get('/{controller}/{method}','{controller}:method');

/* Passing value via parameter */
Router::get('/my-account/{teste}','Controller\\User:my_account');

/* Middleware example */
//Router::get('/my-account','Controller\\User:my_account')->middleware(['\Example\Middleware\Auth::class','Lasted']);



Router::get('/my-account',function(){
    echo 'is Ok!';
})->middleware(['\Example\Middleware\Auth::class','Lasted']);

/* Accessed by all protocols */
Router::any('/',function(){
    //
});
