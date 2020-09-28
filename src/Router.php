<?php

namespace HnrAzevedo\Router;


class Router implements RouterInterface{
    use DefinitionsTrait, RunInTrait, CheckTrait, OwnerTrait, MiddlewareTrait, WhereTrait;

    private array $currentRoute = [];
    private \Closure $beforeAll;
    private \Closure $afterAll;
    private string $host = '';
    private string $prefix = '';
    private bool $loaded = false;

    public static function defineHost(string $host): Router
    {
        self::getInstance()->host = $host;
        return self::getInstance();
    }

    public static function name(string $name): Router
    {
        self::getInstance()->isInNameGroup();
        self::getInstance()->existRouteName($name);
        $route = self::getInstance()->inSave();
        $route['name'] = $name;
        self::getInstance()->routesNames[$name] = $name;
        self::getInstance()->unsetRoute(count(self::getInstance()->routes))->updateRoute($route,$name);
        return self::getInstance();
    }

    public static function group(string $prefix, \Closure $closure): Router
    {
        self::getInstance()->prefix = $prefix;
        self::getInstance()->group = sha1(date('d/m/Y h:m:i'));

        $closure();

        self::getInstance()->group = null;
        self::getInstance()->prefix = null;
        return self::getInstance();
    }

    public static function current(): array
    {
        self::getInstance()->hasCurrentRoute();
        return self::getInstance()->currentRoute;
    }

    public static function currentName(): string
    {
        self::getInstance()->hasCurrentRoute();
        return self::getInstance()->currentRoute['name'];
    }

    public static function currentAction()
    {
        self::getInstance()->hasCurrentRoute();
        return self::getInstance()->currentRoute['action'];
    }

    public static function load(): RouterInterface
    {
        self::getInstance()->loaded = true;

        foreach(self::getInstance()->routes as $r => $route){
            self::getInstance()->currentRoute = $route;
            self::getInstance()->currentRoute['name'] = $r;

            try{
                self::getInstance()->checkMethod($route, $_SERVER['REQUEST_METHOD']);
                self::getInstance()->checkData($route['uri']->getPath(), $_SERVER['REQUEST_URI']);
                
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

        echo '<pre>';
        var_dump(self::getInstance()->currentRoute['uri']->getPath());
        // ...
        return self::getInstance();
    }
   
}