<?php

namespace Filter;

use Exception;

class User{
    
    public function user_in()
    {
        if(!array_key_exists('user',$_SESSION)){
            throw new Exception('User must be logged in.');
        }
    }

}