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
        foreach(self::getInstance()->getPipeline() as $line){
            self::getInstance()->loadLine(new ReflectionObject(new $line()));
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
                if($attr->getName() != 'HnrAzevedo\Router\RouteAttribute') continue;

                $args = $attr->getArguments();
    
                $this->checkArgs($attr->getArguments());
                
                self::set(
                    strtolower(implode('|', $args['methods'])),
                    $args['uri'],
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
        if(!array_key_exists('uri', $args) || !array_key_exists('methods', $args)) {
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
            foreach($attr['attributes'] as $attribute){
                self::getInstance()->attribute($attribute[0], $attribute[1]);
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
