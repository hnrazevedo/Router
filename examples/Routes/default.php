<?php

use HnrAzevedo\Router\Router;


Router::get('/{teste}',function(){
    echo 'teste';
});

Router::get('/1',function(){
    echo 1;
});

Router::get('/3',function(){
    echo 3;
});

/* Returning parameters passed via URL in anonymous functions */
Router::get('/{parameter}/{otherparameter}', function($data){
    echo "Parameter 1:{$data['parameter']}, Parameter 2:{$data['otherparameter']}.";
});

/* Passing controller and/or method via parameter in URL */
Router::get('/{controller}/{method}','{controller}:{method}');

/* Passing value via parameter */
Router::get('/my-account/{teste}','User:my_account');

/* Filter example */
Router::get('/my-account','User:my_account')->filter('User:user_in');


