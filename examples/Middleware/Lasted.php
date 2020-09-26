<?php

namespace Example\Middleware;

use Exception;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Lasted extends Middleware{

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if(!isset($this->error)){
            throw new Exception("Access not belonged: {$this->error}");
        }

        return parent::process($request, $handler);
    }

}