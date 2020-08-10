<?php

use HnrAzevedo\Router\Router;


Router::get('/my-account','User:my_account')->filter('User:user_in');

Router::get('/{parameter}/{otherparameter}', function($data){
    echo "Parameter 1:{$data['parameter']}, Parameter 2:{$data['otherparameter']}.";
});

Router::get('/{controller}/{method}','{controller}:{method}');