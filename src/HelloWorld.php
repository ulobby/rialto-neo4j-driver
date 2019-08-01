<?php
namespace App;

use Nesk\Rialto\AbstractEntryPoint;
use App\HelloWorldProcessDelegate;

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