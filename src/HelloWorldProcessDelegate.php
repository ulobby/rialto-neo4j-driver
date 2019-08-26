<?php

namespace Neo4jBridge;

use Nesk\Rialto\Traits\UsesBasicResourceAsDefault;
use Nesk\Rialto\Interfaces\ShouldHandleProcessDelegation;

class HelloWorldProcessDelegate implements ShouldHandleProcessDelegation
{
    // Define that we want to use the BasicResource class as a default if resourceFromOriginalClassName() returns null
    use UsesBasicResourceAsDefault;

    public function resourceFromOriginalClassName(string $jsClassName): ?string
    {
        // Generate the appropriate class name for PHP
        $class = "Neo4jBridge\{$jsClassName}Resource";

        // If the PHP class doesn't exist, return null, it will automatically create a basic resource.
        return class_exists($class) ? $class : null;
    }
}