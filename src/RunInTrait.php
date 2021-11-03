<?php

declare(strict_types = 1);

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
        $this->beforeAll = (!isset($this->beforeAll)) ? function() {} : $this->beforeAll;
        $this->afterAll = (!isset($this->afterAll)) ? function() {} : $this->afterAll;
        
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
        return self::addInRoute('before', $closure);
    }

    public static function after($closure): RouterInterface
    {
        return self::addInRoute('after', $closure);
    }

    public static function beforeAll($closure, ?array $excepts = null): RouterInterface
    {
        self::getInstance()->setState('before', (is_array($excepts)) ? $excepts : [] ,true);
        self::getInstance()->setState('before', $closure, false);
        return self::getInstance();
    }

    public static function afterAll($closure, ?array $excepts = null): RouterInterface
    {
        self::getInstance()->setState('after', (is_array($excepts)) ? $excepts : [] ,true);
        self::getInstance()->setState('after', $closure, false);
        return self::getInstance();
    }

    public static function beforeGroup($closure, ?array $excepts = null): RouterInterface
    {
        return self::addInRoutes('before', $closure, $excepts);
    }

    public static function afterGroup($closure, ?array $excepts = null): RouterInterface
    {
        return self::addInRoutes('after', $closure, $excepts);
    }

    protected function executeRouteAction($action): bool
    {
        if(is_callable($action)){ 
            
            $params = [];
            $closure = (get_class($action) !== 'Closure') ? $action->getClosure() : $action;
            $ReflectionMethod =  new \ReflectionFunction ($closure);

            foreach($ReflectionMethod->getParameters() as $param){
                if(!isset($_REQUEST[$param->name])) continue;
                $params[$param->name] = $_REQUEST[$param->name];
            }
            
            call_user_func_array($closure, $params);
            return true;
        }

        $this->executeController($action);
        return true;
    }

    private static function addInRoutes(string $state, $closure, ?array $excepts = null): RouterInterface
    {
        self::getInstance()->isInPseudGroup();
        $excepts = (is_array($excepts)) ? $excepts : [];
        $group = self::getInstance()->inSave()['group'];

        foreach(self::getInstance()->getRoutes() as $r => $route){
            if($route['group'] === $group && !in_array($r, $excepts)){
                $route[$state] = (is_null($route[$state])) ? [ $closure ] : array_merge($route[$state], [ $closure ]); 
                self::getInstance()->updateRoute($route, $r);
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
        $this->executeState('after');

        if(!in_array($this->currentName(), (array) $this->getState('after', true))){
            ($this->getState('after', false))();
        }
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
            $path = urldecode(unserialize($this->current()['uri'])->getPath());
            throw new \RuntimeException("Misconfigured route action ({$path})");
        }

        $controller = (string) explode('@',$controllerMeth)[0];
        $method = (string) explode('@',$controllerMeth)[1];

        $this->checkControllerMeth($controllerMeth);

        $params = [];

        $ReflectionMethod =  new \ReflectionMethod(new $controller(), $method);

        foreach($ReflectionMethod->getParameters() as $param){
            if(!isset($_REQUEST[$param->name])) continue;
            $params[$param->name] = $_REQUEST[$param->name];
        }

        call_user_func_array([(new $controller()),$method], $params);
    }

    private function checkControllerMeth(string $controllerMeth): void
    {
        $routeURI = str_replace(['{?','{','}'],'',urldecode(unserialize($this->current()['uri'])->getPath()));

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
