<?php

namespace Example\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Auth extends Middleware{

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if(!array_key_exists('user',$_SESSION)){
            $this->error = 'Usuário não logado.';
        }

        return parent::process($request, $handler);
    }

}