<?php

declare(strict_types = 1);

namespace HnrAzevedo\Router;

use HnrAzevedo\Http\Factory;
use HnrAzevedo\Http\ServerRequest;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

trait MiddlewareTrait
{
    use Helper, CurrentTrait, RunInTrait;

    protected array $globalMiddlewares = [];
    protected ServerRequest $serverRequest;
    protected array $currentMiddlewares = [];

    public static function globalMiddlewares(array $middlewares): RouterInterface
    {
        foreach($middlewares as $middleware){
            if(!class_exists($middleware)){
                throw new \RuntimeException("Middleware class {$middleware} not exists");
            }
        }
        self::getInstance()->setGlobalMiddlewares($middlewares);
        return self::getInstance();
    }

    protected function setGlobalMiddlewares(array $middlewares): void
    {
        $this->globalMiddlewares = $middlewares;
    }

    public static function middleware(array $middlewares): RouterInterface
    {
        $route = self::getInstance()->inSave();
        $route['middlewares'] = (is_array($route['middlewares'])) ? array_merge($route['middlewares'],$middlewares) : $middlewares;
        self::getInstance()->updateRoute($route,array_key_last(self::getInstance()->getRoutes()));
        return self::getInstance();
    }

    public static function groupMiddlewares(array $middlewares, array $excepts): RouterInterface
    {
        $group = self::getInstance()->inSave()['group'];
        foreach(self::getInstance()->getRoutes() as $r => $route){
            if($route['group'] !== $group || in_array($route['name'], $excepts)){
                continue;
            }

            $route['middlewares'] = array_merge($route['middlewares'], $middlewares);
            self::getInstance()->updateRoute($route, array_key_last(self::getInstance()->getRoutes()));
        }
        return self::getInstance();
    }

    private static function existMiddleware(string $name): void
    {
        if(!class_exists($name) && !array_key_exists($name,self::$globalMiddlewares)){
            throw new \RuntimeException("Middleware {$name} does not exist");
        }
    }

    protected function handleMiddlewares(): void
    {
        $factory = new Factory();

        $this->serverRequest = (!isset($this->serverRequest)) ? $factory->createServerRequest($_SERVER['REQUEST_METHOD'], $this->current()['uri'],['route' => $this->current()]) : $this->serverRequest;
        
        foreach ($this->current()['middlewares'] as $middleware){
            $this->currentMiddlewares[] = (class_exists($middleware)) ? new $middleware() : new $this->globalMiddlewares[$middleware]();
        }

        $this->process($this->serverRequest, new class implements RequestHandlerInterface {
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                return (new Factory())->createResponse(200);
            }
        });

    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if(!$this->getInstance()->loaded()){
            $this->getInstance()->load();
        }

        $route = $this->getInstance()->current();
        if(!empty($route)){
            $route['action'] = function(){
                $this->getInstance()->executeBefore();
                $this->getInstance()->executeRouteAction($this->getInstance()->current()['action']);
                $this->getInstance()->executeAfter();
            };
        }

        return $this->next($handler)->handle($request->withAttribute('route',$route));
    }

    private function next(RequestHandlerInterface $defaultHandler): RequestHandlerInterface
    {
        return new class ($this->currentMiddlewares, $defaultHandler) implements RequestHandlerInterface {
            private RequestHandlerInterface $handler;
            private array $pipeline;

            public function __construct(array $pipeline, RequestHandlerInterface $handler)
            {
                $this->handler = $handler;
                $this->pipeline = $pipeline;
            }

            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                if (!$middleware = array_shift($this->pipeline)) {
                    return $this->handler->handle($request);
                }

                $next = clone $this;
                $this->pipeline = [];

                return $middleware->process($request, $next);
            }
        };
    }
    
}
