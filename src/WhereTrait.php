<?php

namespace HnrAzevedo\Router;

trait WhereTrait
{
    use Helper;

    public static function where(array $wheres): Router
    {
        $route = self::getInstance()->inSabe();
        $route['where'] = (is_array($route['where'])) ? array_merge($route['where'],$wheres) : $wheres;
        self::getInstance()->updateRoute($route,array_key_last(self::getInstance()->routes));
        return self::getInstance();
    }

    protected function checkData(string $routePath, string $uriPath): void
    {
        $routePath = explode('/',urldecode($routePath));
        unset($routePath[0]);
        $uriPath = explode('/',urldecode($uriPath));
        unset($uriPath[0]);

        $this->checkCount($routePath, $uriPath);
        
        $corretRoute = true;
        foreach($routePath as $r => $routeFrag){
            $routeFrag = $this->replaceParam($routeFrag, $uriPath[$r]);

            if($routeFrag !== $uriPath[$r]){
                $corretRoute = false;
            }
        }

        if(!$corretRoute){
            throw new \Exception('continue');
        }
    }

    private function replaceParam(string $ref, string $value): string
    {
        if(((substr($ref,0,1) === '{') && (substr($ref,strlen($ref)-1) === '}'))) {
            return $value;
        } 
        return $ref;
    }

    private function checkCount(array $routePath, array $uriPath): void
    {
        if(count($routePath) !== count($uriPath)){
            throw new \Exception('Continue');
        }
    }

}
