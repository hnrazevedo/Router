<?php

namespace HnrAzevedo\Router\Example\Controllers;

use HnrAzevedo\Router\Route;

class ControllerAttribute{

    #[Route(
        uri:'/fooo/{param}',
        methods:['GET'],
        name:'routeExample',
        before:'HnrAzevedo\Router\Example\Controllers\ControllerAttribute@methodBefore',
        middleware:[],
        attributes:[],
        where:[],
        after:'HnrAzevedo\Router\Example\Controllers\ControllerAttribute@methodAfter',
        )]
    public function method($param)
    {
        echo 'Controller@method executed!'.PHP_EOL."Param:{$param}";
    }

    public function methodBefore(): void
    {
        echo 'methodBefore'.PHP_EOL;
    }

    public function methodAfter(): void
    {
        echo PHP_EOL.'methodAfter';
    }

}