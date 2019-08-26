<?php

use PHPUnit\Framework\TestCase;
use Neo4jBridge\FileSystem;
use Neo4jBridge\HelloWorld;

class ExampleTest extends TestCase
{
	public function testTest()
	{
		$fs = new FileSystem;
		$stats = $fs->statSync(__DIR__ . '/ExampleTest.php');
		$this->assertTrue(true, $stats->isFile());
	}

	public function testHelloWorldConnection()
	{
		$helloWorld = new HelloWorld(["name" => "Simon"]);
		$string = $helloWorld->printHello();
		$this->assertEquals("Hello Simon", $string);
	}
}