<?php

use PHPUnit\Framework\TestCase;
use App\Bridge\Client;

class ClientTest extends TestCase
{
	public function tearDown(): void
	{
		Mockery::close();
	}

	public function testExecutesCypherQuery()
	{
		$bridge = Mockery::mock('App\Neo4jBridge');
		$bridge->shouldReceive('run')->once()->andReturn(new \App\Bridge\ResultSet());
		$query = Mockery::mock("App\Bridge\CypherQuery");
		$query->shouldReceive('getQuery')->once()->andReturn("MATCH (n) RETURN count(n)");
		$query->shouldReceive('getParameters')->once()->andReturn([]);
		$client = new Client($bridge);
		$results = $client->executeCypherQuery($query);
		$this->assertTrue(count($results) == 0);
	}
}