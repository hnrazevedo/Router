<?php

declare(strict_types = 1);

namespace HnrAzevedo\Router;

use ReflectionObject, ReflectionMethod, Exception;

trait AttributeTrait
{
    use Helper, DefinitionsTrait;

    protected array $pipeline = [];

    protected function getPipeline(): array
    {
        return $this->pipeline;
    }

    protected function setPipeline(array $pipe): void
    {
        $this->pipeline = $pipe;
    }

    public static function pipeline(array $pline): void
    {
        self::getInstance()->setPipeline($pline);
    }

    public static function loadPipeline(): void
    {
        foreach(self::getInstance()->getPipeline() as $path){

            if(!is_dir($path)){
                self::getInstance()->loadLine(new ReflectionObject(new $path()));
                continue;
            };

            foreach (scandir($path) as $routeFile) {
                if(pathinfo($path . DIRECTORY_SEPARATOR . $routeFile, PATHINFO_EXTENSION) === 'php'){

                    require_once($path . DIRECTORY_SEPARATOR . $routeFile);

                    $classes = get_declared_classes();
                    $className = end($classes);
                    self::getInstance()->loadLine(new ReflectionObject(new $className()));
                }
            }
        }
    }

    private function loadLine(ReflectionObject $reflection): void
    {
        foreach($reflection->getMethods() as $method){
            $this->loadMethod($method);
        }
    }

    private function loadMethod(ReflectionMethod $method): void
    {
        try{
            foreach ($method->getAttributes() as $attr) {
                if($attr->getName() != 'HnrAzevedo\Router\Route') continue;

                $args = $attr->getArguments();
    
                $this->checkArgs($attr->getArguments());
                
                self::set(
                    (array_key_exists('methods', $args)) ? strtolower(implode('|', $args['methods'])) : 'get',
                    (array_key_exists('uri', $args)) ? $args['uri'] : $args[0],
                    $method->class.'@'.$method->name
                );
                
                $this->attrName($args)
                    ->attrBefore($args)
                    ->attrAfter($args)
                    ->attrAttributes($args)
                    ->attrWhere($args)
                    ->attrMiddleware($args);
            }
        }catch(Exception $er){
            throw new Exception('Failed to add route via attribute: '.$er->getMessage());
        }
    }

    private function checkArgs(array $args): self
    {
        if(!array_key_exists('uri', $args) && !array_key_exists(0, $args)) {
            throw new Exception('Misconfigured route attribute');
        }
        return $this;
    }

    private function attrName(array $attr): self
    {
        if(array_key_exists('name', $attr)) {
            self::getInstance()->name($attr['name']);
        }
        return $this;
    }

    private function attrBefore(array $attr): self
    {
        if(array_key_exists('before', $attr)) {
            self::getInstance()->before($attr['before']);
        }
        return $this;
    }

    private function attrAfter(array $attr): self
    {
        if(array_key_exists('after', $attr)) {
            self::getInstance()->after($attr['after']);
        }
        return $this;
    }

    private function attrAttributes(array $attr): self
    {
        if(array_key_exists('attributes', $attr)) {
            foreach($attr['attributes'] as $attribute => $attrValue){
                self::getInstance()->attribute($attribute, $attrValue);
            }
        }
        return $this;
    }

    private function attrWhere(array $attr): self
    {
        if(array_key_exists('where', $attr)) {
            self::getInstance()->where($attr['where']);
        }
        return $this;
    }

    private function attrMiddleware(array $attr): self
    {
        if(array_key_exists('middleware', $attr)) {
            self::getInstance()->middleware($attr['middleware']);
        }
        return $this;
    }

}
