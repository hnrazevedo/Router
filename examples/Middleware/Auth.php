<?php

namespace Example\Middleware;

use Exception;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Auth implements MiddlewareInterface{

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if(!array_key_exists('user',$_SESSION)){
            throw new Exception('Usuário não logado.');
        }

        return $handler->handle($request);
    }

}