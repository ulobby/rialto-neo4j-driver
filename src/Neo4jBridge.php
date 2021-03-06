<?php
namespace Neo4jBridge;

use Nesk\Rialto\AbstractEntryPoint;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

use Neo4jBridge\Neo4jConnectionProcessDelegate;

class Neo4jBridge extends AbstractEntryPoint
{

    public function __construct(array $userOptions = [])
    {
    	$log = new Logger('name');
		$log->pushHandler(new StreamHandler('logs/debug.log', Logger::DEBUG));
		$defaultOptions = [
			"log_node_console" => true,
			"logger" => $log
		];
        parent::__construct(__DIR__.'/Neo4jConnectionDelegate.js', new Neo4jConnectionProcessDelegate, $defaultOptions, $userOptions);
    }
}