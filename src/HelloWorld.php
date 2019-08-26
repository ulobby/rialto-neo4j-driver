<?php
namespace Neo4jBridge;

use Nesk\Rialto\AbstractEntryPoint;
use Neo4jBridge\HelloWorldProcessDelegate;

class HelloWorld extends AbstractEntryPoint
{
    public function __construct(array $userOptions = [])
    {
    	$defaultOptions = [
    		"log_node_console" => true
    	];
        parent::__construct(__DIR__.'/HelloWorldConnectionDelegate.js', new HelloWorldProcessDelegate, $defaultOptions, $userOptions);
    }
}