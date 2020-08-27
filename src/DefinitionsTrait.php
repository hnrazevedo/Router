<?php

namespace HnrAzevedo\Router;

trait DefinitionsTrait{
    private static $instance = null;

    public static function match(string $protocols, string $uri, $walking): Router
    {
        foreach(explode('|',$protocols) as $protocol){
            self::getInstance()->add($uri, $walking, $protocol);
        }
        return self::$instance;
    }

    public static function any(string $uri, $walking): Router
    {
        self::getInstance()->add($uri, $walking, 'get');
        self::getInstance()->add($uri, $walking, 'post');
        self::getInstance()->add($uri, $walking, 'form');
        return self::getInstance()->add($uri, $walking, 'ajax');
    }

    public static function get(string $uri, $walking): Router
    {
        return self::getInstance()->add($uri, $walking, 'get');
    }

    public static function post(string $uri, $walking): Router
    {
        return self::getInstance()->add($uri, $walking, 'post');
    }

    public static function ajax(string $uri, $walking): Router
    {
        return self::getInstance()->add($uri, $walking, 'ajax');
    }

    public static function put(string $uri, $walking): Router
    {
        return self::getInstance()->add($uri, $walking, 'put');
    }

    public static function patch(string $uri, $walking): Router
    {
        return self::getInstance()->add($uri, $walking, 'patch');
    }

    public static function head(string $uri, $walking): Router
    {
        return self::getInstance()->add($uri, $walking, 'head');
    }

    public static function trace(string $uri, $walking): Router
    {
        return self::getInstance()->add($uri, $walking, 'trace');
    }

    public static function options(string $uri, $walking): Router
    {
        return self::getInstance()->add($uri, $walking, 'options');
    }

    public static function connect(string $uri, $walking): Router
    {
        return self::getInstance()->add($uri, $walking, 'connect');
    }

    public static function delete(string $uri, $walking): Router
    {
        return self::getInstance()->add($uri, $walking, 'delete');
    }

    public static function form(string $uri, $walking): Router
    {
        return self::getInstance()->add($uri, $walking, 'form');
    }

    public static function add(string $uri, $walking, string $protocol): Router
    {
        return self::getInstance()->set($uri, $walking, $protocol);
    }

}