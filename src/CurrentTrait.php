<?php

namespace HnrAzevedo\Router;

trait CurrentTrait
{
    use Helper;

    protected array $currentRoute = [];

    public static function current(): array
    {
        self::getInstance()->hasCurrentRoute();
        return self::getInstance()->getCurrent();
    }

    public static function currentName(): string
    {
        self::getInstance()->hasCurrentRoute();
        return self::getInstance()->getCurrent()['name'];
    }

    public static function currentAction()
    {
        self::getInstance()->hasCurrentRoute();
        return self::getInstance()->getCurrent()['action'];
    }

    protected function getCurrent(): array
    {
        return $this->currentRoute;
    }
    
}
