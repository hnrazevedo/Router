<?php

use HnrAzevedo\Router\Router;

/* DEFINITIONS */

/**
 * Defines a route for the GET method
 */
Router::get('/foo','\HnrAzevedo\Router\Examples\Controller@method');

/**
 * Defines a route to the GET method with an anonymous function
 */
Router::get('/bar', function(){
    //
});

/**
 * Defines a route to the POST method with an anonymous function
 */
Router::post('/bar', function(){
    //
});

/**
 * Defining route for any method
 */
Router::any('/any', function(){
    //
});

/**
 * Defining route for various methods
 * @param string $methods
 * @param string $route
 * @param \Closure|string $action
 */
Router::match('GET|post', '/get_post', function(){
    //
});

/**
 * Defines the route name
 */
Router::get('/named', function(){
    //
})->name('baz');




/* ANONYMOUS EXECTIONS */

/**
 * Run before all requests, regardless of error occurrences
 */
Router::beforeAll(function(){
    //
}, null);

/**
 * Run after all requests, regardless of error occurrences
 */
Router::afterAll(function(){
    //
}, null);

/**
 * Executes after executing the triggered route action
 */
Router::get('/after', function(){
    //
})->after(function(){
    //
});

/**
 * Executes before executing the triggered route action
 */
Router::get('/before', function(){
    //
})->before(function(){
    //
});
/**
 * Execution order:
 * - beforeAll
 * - before
 * - Route Action
 * - after
 * - afterAll
 */





/* PARAMETERS */

/**
 * Example of route definition with parameters
 */
Router::get('/passingParameters/{param}', function($param){
    echo $param;
});

/**
 * Example of setting an optional parameter
 */
Router::get('/passingParameters/{:optionalParam}', function($optionalParam){
    echo $optionalParam;
});

/**
 * Example of regular expression test for parameters
 */
Router::get('/testingParameters/{param}', function($param){
    echo $param;
})->where([
    'param'=>'[0-9]{1,11}'
]);




/* GROUPS */

/**
 * Defining route groups
 * @param string $prefix
 * @param \Closure $routeDefinitions
 */
Router::group('/admin', function(){
    Router::get('/users/{teste}', function($teste){
        echo $teste;
    })->where([
        'teste'=>'[a-zA-Z]*'
    ]);
});


Router::group('/admin2', function(){
    Router::get('/users/{teste}', function($teste){
        echo $teste;
    })->name('teste');
})->groupWhere([
    'teste'=>'[a-zA-Z]*'
],[]);




/* MIDDLEWARES */

/**
 * Defining nicknames for middleware
 */
Router::globalMiddlewares([
    'Lasted'=> \HnrAzevedo\Router\Example\Middleware\Lasted::class
]);

/**
 * Defining route middleware - implements Psr\Http\Server\MiddlewareInterface
 */
Router::get('/passingParameters/{:optionalParam}', function($optionalParam = null){
    echo $optionalParam;
})->middleware([
    HnrAzevedo\Router\Example\Middleware\Auth::class
]);

/**
 * Defining middleware by nickname
 */
Router::get('/lasted', function(){
    //
})->middleware([
    'Lasted'
]);

/**
 * Defining multiple middlewares
 * NOTE: Importantly, the execution follows the same order of definition
 */
Router::get('/middlewares', function(){
    //
})->middleware([
    HnrAzevedo\Router\Example\Middleware\Auth::class,
    'Lasted'
]);