<?php

namespace HnrAzevedo\Router\Example\Middleware;

use Exception;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;

/** 
  * @property string $error
  */ 
class Lasted extends Middleware{

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        var_dump('Lasted');
        if(!isset($this->error)){
            throw new Exception("Access not belonged: {$this->error}");
        }

        return $handler->handle($request, $handler);
    }

}