<?php

namespace HnrAzevedo\Router;

use Attribute;

#[Attribute]
class Route
{
    public function __construct(
        private string $uri,
        private array $methods,
        private string $name,
        private ?array $where,
        private ?string|?\Closure $after,
        private ?string|?\Closure $before,
        private ?array $attributes,
        private ?array $middleware
    ) {}
}
