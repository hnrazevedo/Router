<?php

use HnrAzevedo\Router\Router;

/* Returning parameters passed via URL in anonymous functions */
Router::get('/{parameter}/{otherparameter}', function($parameter, $otherparameter){
    echo "Parameter 1:{$parameter}, Parameter 2:{$otherparameter}.";
});

/* Passing controller and/or method via parameter in URL */
Router::get('/{controller}/{method}','{controller}:{method}');
Router::get('/{controller}/{method}','{controller}:method');

/* Passing value via parameter */
Router::get('/my-account/{teste}','User:my_account');

/* Filter example */
Router::get('/my-account','User:my_account')->filter('User:user_in');

/* Accessed by all protocols */
Router::any('/',function(){
    //
});
