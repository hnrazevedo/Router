<?php

namespace HnrAzevedo\Router;

trait ExtraJobsTrait{

    public function before($walking): Router
    {
        return $this->setOnRoute($walking,'before');
    }

    public static function beforeAll($walking, $except = null): Router
    {
        $excepts = is_array($except) ? $except : [$except];
        self::getInstance()->beforeExcepts = $excepts;
        self::getInstance()->beforeAll = $walking;
        return self::getInstance()->setOnRoutes($walking,'beforeAll',$excepts);
    }

    public function after($walking): Router
    {
        return $this->setOnRoute($walking,'after');
    }

    public static function afterAll($walking, $except = null): Router
    {
        $excepts = is_array($except) ? $except : [$except];
        self::getInstance()->afterExcepts = $excepts;
        self::getInstance()->afterAll = $walking;
        return self::getInstance()->setOnRoutes($walking,'afterAll',$excepts);
    }

    private function setOnRoute($walking, string $state): Router
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
        
        $this->routers[count($this->routers)-1][$state] = $walking;
        return $this;
    }

    private function setOnRoutes($walking, string $state, array $excepts): Router
    {
        foreach($this->routers as $r => $route){
            if(!in_array($this->routers[$r]['name'],$excepts)){
                $this->routers[$r][$state] = $walking;
            }
        }
        return $this;
    }

}
