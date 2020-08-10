<?php

namespace Example\Controllers;

class User{

    public function my_account(array $data): void
    {
        var_dump($data['GET']);
        echo 'my_account';
    }

}