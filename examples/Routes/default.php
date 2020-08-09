<?php

use HnrAzevedo\Router\Router;

Router::get('/','Application:index')->filter('User:user_in');

Router::get('/{controller}/{method}','{controller}:{method}');