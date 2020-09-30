<?php

namespace HnrAzevedo\Router;

trait CurrentTrait{
    use Helper;

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
    
}
