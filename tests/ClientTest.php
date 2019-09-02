<?php

use PHPUnit\Framework\TestCase;
use Neo4jBridge\Bridge\Client;
use Neo4jBridge\Bridge\ResultSet;

class ClientTest extends TestCase
{
	public function tearDown(): void
	{
		Mockery::close();
	}

	public function testExecutesCypherQuery()
	{
		$bridge = Mockery::mock('Neo4jBridge\Neo4jBridge');
		$client = new Client($bridge);
		$results = new Neo4jBridge\Bridge\ResultSet($client, ['data' => [], 'columns' => []]);
		$bridge->shouldReceive('run')->once()->andReturn($results);
		$query = Mockery::mock("Neo4jBridge\Bridge\CypherQuery")->shouldAllowMockingProtectedMethods()->makePartial();
		$query->shouldReceive('getQuery')->andReturn("MATCH (n) RETURN count(n)");
		$query->shouldReceive('getParameters')->once()->andReturn([]);
		$query->shouldReceive('getExpectedColumns')->andReturn([]);
		$query->shouldReceive('parseColumnsFromQuery')->andReturn(['count(n)']);
		$results = $client->executeCypherQuery($query);
		$this->assertTrue(count($results) == 0);
	}
}