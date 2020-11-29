<?php

declare(strict_types = 1);

namespace HnrAzevedo\Router;

use HnrAzevedo\Http\Uri;

final class Router implements RouterInterface
{
    use DefinitionsTrait, 
        RunInTrait, 
        CheckTrait, 
        OwnerTrait, 
        MiddlewareTrait, 
        WhereTrait,
        PrioritizeTrait,
        CurrentTrait;

    private array $groupsName = [];
    private ?\Exception $error = null;

    public static function name(string $name): Router
    {
        self::getInstance()->existRouteName($name);
        $route = self::getInstance()->inSave();
        $route['name'] = $name;
        self::getInstance()->routesName[$name] = $name;
        self::getInstance()->unsetRoute(array_key_last(self::getInstance()->getRoutes()))->updateRoute($route, $name);
        return self::getInstance();
    }

    public static function attribute(string $name, $value = null)
    {
        $route = self::getInstance()->inSave();

        if(null === $value){
            return (isset($route['attributes'][$name])) ? $route['attributes'][$name] : null;
        }

        self::getInstance()->throwCallable($value);
        $route['attributes'][$name] = $value;
        self::getInstance()->updateRoute($route, array_key_last(self::getInstance()->getRoutes()));
        return self::getInstance();
    }

    public static function groupAttribute(string $name, $value = null, ?array $excepts = []): Router
    {
        $group = self::getInstance()->inSave()['group'];
        foreach(self::getInstance()->getRoutes() as $r => $route){
            if($route['group'] !== $group || in_array($route['name'], $excepts)){
                continue;
            }

            $route['attributes'] = array_merge([$name => $value], $route['attributes']);
            self::getInstance()->updateRoute($route, $r);
        }
        return self::getInstance();
    }

    public static function group(string $prefix, \Closure $closure): Router
    {
        $id = sha1(date('d/m/Y h:m:i'));
        while(array_key_exists($id, self::getInstance()->groupsName)){
            $id = sha1(date('d/m/Y h:m:i').$id);
        }

        self::getInstance()->groupsName[$id] = $id;
        
        self::getInstance()->setPrefix($prefix);
        
        self::getInstance()->setGroup($id);

        $closure();

        self::getInstance()->setGroup(null);
        self::getInstance()->setPrefix('');
        return self::getInstance();
    }

    public static function load(?string $name = null): RouterInterface
    {
        self::getInstance()->loaded = true;

        if(null !== $name){
            return self::getInstance()->loadByName($name);
        }

        self::getInstance()->sortRoutes();

        $requestMethod = (isset($_REQUEST['_METHOD'])) ? $_REQUEST['_METHOD'] : $_SERVER['REQUEST_METHOD'];

        foreach(self::getInstance()->getRoutes() as $r => $route){
            self::getInstance()->currentRoute = $route;
            self::getInstance()->currentRoute['name'] = $r;

            try{
                self::getInstance()->checkMethod($route, $requestMethod);
                self::getInstance()->checkData($route, (new Uri($_SERVER['REQUEST_URI']))->getPath());
                return self::getInstance();
            }catch(\Exception $er){
                continue;
            }
        }
        
        self::getInstance()->currentRoute = [];

        self::getInstance()->error = new \Exception('Not found', 404);

        return self::getInstance();
    }

    public static function run(?string $name = null): RouterInterface
    {
        if(!self::getInstance()->loaded){
            self::getInstance()->load($name);
        }

        self::getInstance()->checkError();

        self::getInstance()->handleMiddlewares();

        self::getInstance()->executeBefore();
        
        try{
            $action = unserialize(self::getInstance()->current()['action']);
            self::getInstance()->executeRouteAction( (is_string($action)) ? $action : $action->getClosure()  );
        }catch(\Exception $er){
            self::getInstance()->error = $er;
        }
        
        self::getInstance()->executeAfter();
        
        self::getInstance()->checkError();

        return self::getInstance();
    }

    private function checkError(): void
    {
        if(isset($this->error)){
            throw $this->error;
        }
    }

    private function loadByName(string $name): RouterInterface
    {
        $this->hasRouteName($name);
        $this->currentRoute = $this->getRoutes()[$name];
        return $this;
    }

    public static function routes(?array $routes = null): array
    {
        if(null !== $routes){
            self::getInstance()->setRoutes($routes);
            self::getInstance()->sortRoutes();
        }
        return self::getInstance()->routes;
    }
   
}
