<?php

namespace Example\Filters;

use HnrAzevedo\Filter\Filter as HnrFilter;

class User extends HnrFilter{

    public function user_in(): bool
    {
        $this->addMessage('user_in','User required to be logged in.');

        $this->addTreat('user_in','report_notLogged');

        return (array_key_exists('user',$_SESSION));
    }

    public function report_notLogged(): void
    {

    }

}