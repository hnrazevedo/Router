<?php

namespace HnrAzevedo\Router\Example\Controllers;

class User{

    public function requireLogin($param, $param2): void
    {
        echo "{$param} - {$param2}";
    }

}