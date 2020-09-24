<?php

namespace HnrAzevedo\Router;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use HnrAzevedo\Http\Response;

class RequestHandler implements RequestHandlerInterface
{
    private static Response $response;

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        if(!isset(self::$response)){
            self::$response = new Response();
        }

        return self::$response;
    }
}