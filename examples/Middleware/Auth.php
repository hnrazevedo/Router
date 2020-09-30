<?php

namespace Example\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;

/** 
  * @property string $error
  */ 
class Auth extends Middleware{

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if(!array_key_exists('user',$_SESSION)){
            $this->error = 'The user must be logged in to the system';
        }

        return parent::process($request, $handler);
    }

}