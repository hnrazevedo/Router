<?php

use HnrAzevedo\Router\Router;

/* Never access */
Router::get('/my-account','User:my_account')->filter('User:user_in');


Router::get('/{teste}',function(){
    echo 'teste';
});

Router::get('/1',function(){
    echo 1;
});

Router::get('/3',function(){
    echo 3;
});


Router::get('/{parameter}/{otherparameter}', function($data){
    echo "Parameter 1:{$data['parameter']}, Parameter 2:{$data['otherparameter']}.";
});

Router::get('/{controller}/{method}','{controller}:{method}');