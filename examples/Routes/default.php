<?php

use HnrAzevedo\Router\Router;

Router::get('/','Application:index');

Router::get('/{controller}/{method}','{controller}:{method}');