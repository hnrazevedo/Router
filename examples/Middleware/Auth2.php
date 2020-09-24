<?php

namespace Example\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Auth2 extends Middleware{

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if(!isset($this->error)){
            var_dump($this->error);
            die();
        }

        return parent::process($request, $handler);
    }

}