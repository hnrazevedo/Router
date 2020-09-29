<?php

namespace HnrAzevedo\Router;

trait WhereTrait
{
    use Helper;

    public static function where(array $wheres): Router
    {
        $route = self::getInstance()->inSave();
        $route['where'] = (is_array($route['where'])) ? array_merge($route['where'],$wheres) : $wheres;
        self::getInstance()->updateRoute($route,array_key_last(self::getInstance()->routes));
        return self::getInstance();
    }

    protected function checkData(array $route, string $uriPath): void
    {
        $this->checkCount($route['uri']->getPath(), $uriPath);

        $uriPath .= (substr($uriPath,strlen($uriPath)-1) !== '/') ? '/' : '';

        $routePath = explode('/',urldecode($route['uri']->getPath()));
        unset($routePath[0]);
        $uriPath = explode('/',urldecode($uriPath));
        unset($uriPath[0]);

        $corretRoute = true;
        foreach($routePath as $r => $routeFrag){
            $where = is_array($route['where']) ? $route['where'] : [];
            $routeFrag = $this->replaceParam($where, $routeFrag, $uriPath[$r]);

            if($routeFrag !== $uriPath[$r]){
                $corretRoute = false;
            }
        }

        if(!$corretRoute){
            throw new \Exception('continue');
        }
    }

    private function replaceParam(array $where, string $ref, string $value): string
    {
        if(((substr($ref,0,1) === '{') && (substr($ref,strlen($ref)-1) === '}'))) {
            if(array_key_exists(str_replace(['{','}',':'],'',$ref),$where)){
                $this->matchParam($where, $ref, $value);
            }
            return $value;
        } 
        return $ref;
    }

    private function checkCount(string $routePath, string $uriPath): void
    {
        $countRequest = substr_count($uriPath,'/') - substr_count($routePath,'{:');
        $countRoute = substr_count($routePath,'/') - substr_count($routePath,'{:');

        if($countRequest !== $countRoute){
            throw new \Exception('continue');
        }
    }

    private function matchParam(array $where, string $ref, string $value): void
    {
        if(substr($ref,0,2) === '{:' || $value !== ''){
            if(!preg_match("/^{$where[str_replace(['{','}',':'],'',$ref)]}$/",$value)){
                throw new \Exception('continue');
            }
        }
    }

}
