<?php

namespace Example\Controllers;

use HnrAzevedo\Router\Controller;

class User extends Controller{

    public function my_account(array $data): void
    {
        echo 'my_account';
    }

}