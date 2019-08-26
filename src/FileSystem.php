<?php
namespace Neo4jBridge;

use Nesk\Rialto\AbstractEntryPoint;

class FileSystem extends AbstractEntryPoint
{
    public function __construct()
    {
        parent::__construct(__DIR__.'/FileSystemConnectionDelegate.js');
    }
}