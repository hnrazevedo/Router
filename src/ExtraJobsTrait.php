<?php

namespace HnrAzevedo\Router;

trait ExtraJobsTrait{
    protected array $routers = [];

    public function before($walking)
    {
        return $this->setOnRoute($walking,'before');
    }

    public static function beforeAll($walking, $except = null)
    {
        $excepts = is_array($except) ? $except : [$except];
        self::getInstance()->beforeExcepts = $excepts;
        self::getInstance()->beforeAll = $walking;
        return self::getInstance()->setOnRoutes($walking,'beforeAll',$excepts);
    }

    public function after($walking)
    {
        return $this->setOnRoute($walking,'after');
    }

    public static function afterAll($walking, $except = null)
    {
        $excepts = is_array($except) ? $except : [$except];
        self::getInstance()->afterExcepts = $excepts;
        self::getInstance()->afterAll = $walking;
        return self::getInstance()->setOnRoutes($walking,'afterAll',$excepts);
    }

    private function setOnRoute($walking, string $state)
    {
        if($this->lastReturn !== null){
            $currentGroup = end($this->routers)['group'];

            foreach ($this->routers as $key => $value) {

                if($value['group'] === $currentGroup){
                    $this->routers[$key][$state] = $walking;
                }

            }
            return $this;
        }
        
        $this->routers[array_key_last($this->routers)][$state] = $walking;
        return $this;
    }

    private function setOnRoutes($walking, string $state, array $excepts)
    {
        foreach($this->routers as $r => $route){
            if(!in_array($this->routers,$excepts)){
                $this->routers[$r][$state] = $walking;
            }
        }
        return $this;
    }

}
