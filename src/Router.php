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

    private ?\Exception $error = null;

    public static function name(string $name): Router
    {
        self::getInstance()->isInNameGroup();
        self::getInstance()->existRouteName($name);
        $route = self::getInstance()->inSave();
        $route['name'] = $name;
        self::getInstance()->routesName[$name] = $name;
        self::getInstance()->unsetRoute(array_key_last(self::getInstance()->getRoutes()))->updateRoute($route,$name);
        return self::getInstance();
    }

    public static function group(string $prefix, \Closure $closure): Router
    {
        self::getInstance()->setPrefix($prefix);
        self::getInstance()->setGroup(sha1(date('d/m/Y h:m:i')));

        $closure();

        self::getInstance()->setGroup(null);
        self::getInstance()->setPrefix('');
        return self::getInstance();
    }

    public static function load(): RouterInterface
    {
        self::getInstance()->loaded = true;
        self::getInstance()->sortRoutes();

        foreach(self::getInstance()->getRoutes() as $r => $route){
            self::getInstance()->currentRoute = $route;
            self::getInstance()->currentRoute['name'] = $r;

            try{
                self::getInstance()->checkMethod($route, $_SERVER['REQUEST_METHOD']);
                self::getInstance()->checkData($route, (new Uri($_SERVER['REQUEST_URI']))->getPath());
                return self::getInstance();
            }catch(\Exception $er){
                continue;
            }
        }
        
        self::getInstance()->currentRoute = [];
        throw new \Exception('Page not found', 404);
    }

    public static function run(): RouterInterface
    {
        if(!self::getInstance()->loaded){
            self::getInstance()->load();
        }

        self::getInstance()->handleMiddlewares();

        self::getInstance()->executeBefore();
        
        try{
            self::getInstance()->executeRouteAction();
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
   
}
