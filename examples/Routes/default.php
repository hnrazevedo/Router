<?php

use HnrAzevedo\Router\Router;


Router::beforeAll(function(){
    echo 'beforeAll<br>';
},['testes']);

Router::afterAll(function(){
    echo 'afterAll<br>';
},['testes']);

Router::match('GET|POST|AJAX','/{parameter}/{otherparameter}', function($parameter, $otherparameter){
    echo "Parameter 1:{$parameter}, Parameter 2:{$otherparameter}.";
});

Router::get('/{controller}/{method}','{controller}:{method}');

Router::get('/my-account','Controller\\User:my_account')->before(function(){
    echo '1';
});

Router::get('/my-account/teste/teste','Controller\\User:my_account')->where([
    'teste'=>'[a-zA-Z]{1,10}',
    'teste2' => '[0-9]{1}'
])->name('testes');

Router::get('/my-account/{:teste}/{teste2}','Controller\\User:my_account')->where([
    'teste'=>'[a-zA-Z]{1,10}',
    'teste2'=>'[a-zA-Z]{1,10}'
]);

Router::get('/my-account/{:teste}','Controller\\User:my_account')->where([
    'teste'=>'[a-zA-Z]{1,10}'
]);

Router::get('/my-account1',function(){
    echo 'is Ok!';
})->middleware(['\Example\Middleware\Auth::class','Lasted']);

Router::any('/',function(){
    //
})->name('index');
