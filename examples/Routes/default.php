<?php

use HnrAzevedo\Router\Router;

Router::get('/','Application:index');

Router::get('/{parameter}/{teste}','Params:index');