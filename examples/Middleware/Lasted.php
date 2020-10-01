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

        echo '11111111111';
        if(!isset($this->error)){
            throw new Exception("Access not belonged: {$this->error}");
        }

        return parent::process($request, $handler);
    }

}