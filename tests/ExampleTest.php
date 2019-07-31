<?php

use PHPUnit\Framework\TestCase;
use App\FileSystem;

class ExampleTest extends TestCase
{
	public function testTest()
	{
		$fs = new FileSystem;
		$stats = $fs->statSync(__DIR__ . '/ExampleTest.php');
		$this->assertTrue(true, $stats->isFile());
	}
}