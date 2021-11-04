<?php

use HnrAzevedo\Router\Router;

/*Router::pipeline([
    HnrAzevedo\Router\Example\Controllers\ControllerAttribute::class
]);*/

Router::pipeline([
    HnrAzevedo\Router\Example\Controllers\ControllerAttribute::class,
    'examples\Controllers'
]);