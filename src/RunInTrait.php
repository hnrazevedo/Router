<?php

namespace HnrAzevedo\Router;

trait RunInTrait
{
    use Helper, 
        CheckTrait, 
        CurrentTrait;

    protected array $beforeExcepts = [];
    protected array $afterExcepts = [];
    protected \Closure $beforeAll;
    protected \Closure $afterAll;

    protected function getState(string $state, bool $except = false)
    {
        if($state === 'before'){
            return ($except) ? $this->beforeExcepts : $this->beforeAll;
        }

        return ($except) ? $this->afterExcepts : $this->afterAll;
    }

    protected function setState(string $state, $settable, bool $except = false): bool
    {
        if($state === 'before'){
            if($except){
                $this->beforeExcepts = $settable;
                return true;
            }

            $this->beforeAll = $settable;
            return true;
        }

        if($except){
            $this->afterExcepts = $settable;
            return true;
        }
            
        $this->afterAll = $settable;
        return true;
    }

    public static function before($closure): RouterInterface
    {
        return self::addInRoute('before',$closure);
    }

    public static function after($closure): RouterInterface
    {
        return self::addInRoute('after',$closure);
    }

    public static function beforeAll($closure, $excepts): RouterInterface
    {
        self::getInstance()->setState('before', (is_array($excepts)) ? $excepts : [ $excepts ] ,true);
        self::getInstance()->setState('before', $closure, false);
        return self::getInstance();
    }

    public static function afterAll($closure, $excepts): RouterInterface
    {
        self::getInstance()->setState('after', (is_array($excepts)) ? $excepts : [ $excepts ] ,true);
        self::getInstance()->setState('after', $closure, false);
        return self::getInstance();
    }

    public static function beforeGroup($closure, $excepts): RouterInterface
    {
        return self::addInRoutes('before', $closure, $excepts);
    }

    public static function afterGroup($closure, $excepts): RouterInterface
    {
        return self::addInRoutes('after', $closure, $excepts);
    }

    protected function executeRouteAction(): bool
    {
        if(is_callable($this->current()['action'])){        
            call_user_func_array($this->current()['action'], $_REQUEST);
            return true;
        }

        $this->executeController($this->current()['action']);
        return true;
    }

    private static function addInRoutes(string $state, $closure, $excepts): RouterInterface
    {
        self::getInstance()->isInPseudGroup();
        $excepts = (is_array($excepts)) ? $excepts : [ $excepts ];
        $group = self::getInstance()->inSave()['group'];

        foreach(self::getInstance()->getRoutes() as $r => $route){
            if($route['group'] === $group && !in_array($r,$excepts)){
                self::getInstance()->getRoutes()[$r][$state] = (is_null($route[$state])) ? [ $closure ] : array_merge($route[$state], [ $closure ]); 
            }
        }

        return self::getInstance();
    }

    private static function addInRoute(string $state, $closure): RouterInterface
    {
        $route = self::getInstance()->inSave();
        $route[$state] = (!is_null($route[$state])) ? [ $closure ] : array_merge($route[$state], [ $closure ]);
        self::updateRoute($route,array_key_last(self::getInstance()->getRoutes()));
        return self::getInstance();
    }

    protected function executeBefore(): void
    {
        if(!in_array($this->currentName(), (array) $this->getState('before', true))){
            ($this->getState('before', false))();
        }

        $this->executeState('before');
    }

    protected function executeAfter(): void
    {
        if(!in_array($this->currentName(), (array) $this->getState('after', true))){
            ($this->getState('after', false))();
        }

        $this->executeState('after');
    }

    private function executeState(string $stage): void
    {
        foreach($this->current()[$stage] as $state){
            if(is_callable($state)){
                $state();
                continue;
            }

            $this->executeController($state);
        }
    }

    private function executeController(string $controllerMeth): void
    {
        if(count(explode('@',$controllerMeth)) !== 2){
            $path = urldecode($this->current()['uri']->getPath());
            throw new \RuntimeException("Misconfigured route action ({$path})");
        }

        $controller = (string) explode('@',$controllerMeth)[0];
        $method = (string) explode('@',$controllerMeth)[1];

        $this->checkControllerMeth($controllerMeth);

        call_user_func_array([(new $controller()),$method], $_REQUEST);
    }

    private function checkControllerMeth(string $controllerMeth): void
    {
        $routeURI = str_replace(['{:','{','}'],'',urldecode($this->current()['uri']->getPath()));

        $controller = (string) explode('@',$controllerMeth)[0];
        $method = (string) explode('@',$controllerMeth)[1];

        if(!class_exists($controller)){
            throw new \RuntimeException("Controller not found in route URI {$routeURI}");
        }

        if(!method_exists($controller, $method)){
            throw new \RuntimeException("Method {$method} not found in controller {$controller} in route URI {$routeURI}");
        }
        
    }

}
