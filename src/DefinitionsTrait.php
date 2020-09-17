<?php

namespace HnrAzevedo\Router;

trait DefinitionsTrait{
    private static $instance = null;

    public static function match(string $protocols, string $uri, $walking)
    {
        foreach(explode('|',$protocols) as $protocol){
            self::getInstance()->add($uri, $walking, $protocol);
        }
        return self::$instance;
    }

    public static function any(string $uri, $walking)
    {
        return self::getInstance()->add($uri, $walking, 'get')->add($uri, $walking, 'post')->add($uri, $walking, 'form')->add($uri, $walking, 'ajax');
    }

    public static function get(string $uri, $walking)
    {
        return self::getInstance()->add($uri, $walking, 'get');
    }

    public static function post(string $uri, $walking)
    {
        return self::getInstance()->add($uri, $walking, 'post');
    }

    public static function ajax(string $uri, $walking)
    {
        return self::getInstance()->add($uri, $walking, 'ajax');
    }

    public static function put(string $uri, $walking)
    {
        return self::getInstance()->add($uri, $walking, 'put');
    }

    public static function patch(string $uri, $walking)
    {
        return self::getInstance()->add($uri, $walking, 'patch');
    }

    public static function options(string $uri, $walking)
    {
        return self::getInstance()->add($uri, $walking, 'options');
    }

    public static function delete(string $uri, $walking)
    {
        return self::getInstance()->add($uri, $walking, 'delete');
    }

    public static function form(string $uri, $walking)
    {
        return self::getInstance()->add($uri, $walking, 'form');
    }

    public static function add(string $uri, $walking, string $protocol)
    {
        return self::getInstance()->set($uri, $walking, $protocol);
    }

}
