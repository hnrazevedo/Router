<?php

namespace HnrAzevedo\Router;

trait RunInTrait
{
    use Helper, CheckTrait;

    protected array $beforeExcepts = [];
    protected array $afterExcepts = [];

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
        self::getInstance()->beforeExcepts = (is_array($excepts)) ? $excepts : [ $excepts ];
        self::getInstance()->beforeAll = $closure;
        return self::getInstance();
    }

    public static function afterAll($closure, $excepts): RouterInterface
    {
        self::getInstance()->afterExcepts = (is_array($excepts)) ? $excepts : [ $excepts ];
        self::getInstance()->afterAll = $closure;
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

    private static function addInRoutes(string $state, $closure, $excepts): RouterInterface
    {
        self::getInstance()->isInPseudGroup();
        $excepts = (is_array($excepts)) ? $excepts : [ $excepts ];
        $group = self::getInstance()->inSave()['group'];

        foreach(self::getInstance()->routes as $r => $route){
            if($route['group'] === $group && !in_array($r,$excepts)){
                self::getInstance()->routes[$r][$state] = (is_null($route[$state])) ? [ $closure ] : array_merge($route[$state], [ $closure ]); 
            }
        }

        return self::getInstance();
    }

    private static function addInRoute(string $state, $closure): RouterInterface
    {
        $route = self::getInstance()->inSave();
        $state = (!is_null($route[$state])) ? [ $closure ] : array_merge($route[$state], [ $closure ]);
        $route[$state] = $state;
        self::updateRoute($route,array_key_last(self::getInstance()->routes));
        return self::getInstance();
    }

}
