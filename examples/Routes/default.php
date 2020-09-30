<?php

use HnrAzevedo\Router\Router;



Router::beforeAll(function(){
    echo '<br><b>beforeAll</b><br>';
},['testes']);

Router::afterAll(function(){
    echo '<br><b>afterAll</b><br>';
},['testes']);


Router::match('GET|POST|AJAX','/{1parameter}/{otherparameter}', function($parameter, $otherparameter){
    echo "Parameter 1:{$parameter}, Parameter 2:{$otherparameter}.";
});

Router::get('/{2controller}/{method}','{controller}:{method}');

Router::get('/3my-account','Controller\\User:my_account')->before(function(){
    echo '1';
});

Router::get('/4my-account/teste/teste','Controller\\User:my_account')->name('2');

Router::get('/5my-account/{:teste}/{teste2}',function($teste, $teste2){
    echo $teste.'-'.$teste2;
})->where([
    'teste'=>'[a-zA-Z]{1,10}',
    'teste2'=>'[a-zA-Z]{1,10}'
]);


Router::get('/6my-accounttttt/{param1}/{param2}','HnrAzevedo\Router\Example\Controllers\User@requireLogin');
//->middleware(['\Example\Middleware\Auth::class','Lasted']);

Router::get('/7my-account/{:teste}','Controller\\User:my_account')->where([
    'teste'=>'[a-zA-Z]{1,10}'
]);


Router::any('/8',function(){
    //
})->name('index');
