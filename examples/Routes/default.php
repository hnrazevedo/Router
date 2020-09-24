<?php

use HnrAzevedo\Router\Router;


/* Filter example */
Router::get('/my-account',function(){
    echo 'Ok';
})->middleware('\Example\Middleware\Auth::class');

/* Returning parameters passed via URL in anonymous functions 
Router::get('/{parameter}/{otherparameter}', function($parameter, $otherparameter){
    echo "Parameter 1:{$parameter}, Parameter 2:{$otherparameter}.";
});

/* Passing controller and/or method via parameter in URL 
Router::get('/{controller}/{method}','{controller}:{method}');
Router::get('/{controller}/{method}','{controller}:method');

/* Passing value via parameter 
Router::get('/my-account/{teste}','Controller\\User:my_account');


/* Accessed by all protocols 
Router::any('/',function(){
    //
});
*/