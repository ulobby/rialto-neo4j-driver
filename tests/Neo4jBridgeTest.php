<?php

use PHPUnit\Framework\TestCase;
use App\Neo4jBridge;

class Neo4jBridgeTest extends TestCase
{
	private $driver;

	public function setUp(): void
	{
		$params = [
			"host" => "instance0",
			"user" => "neo4j",
			"password" => "dev"
		];
		$this->driver = new Neo4jConnection($params);
	}

	public function tearDown(): void
	{
		$this->driver->run("MATCH (n) DETACH DELETE n");
		$this->driver->close();
	}

	public function testRunsQueryAndReturnsArray()
	{
		$query = "CREATE (p:Person) SET p.name = {name} RETURN p";
		$params = ["name" => "testy mctesty"];
		$this->driver->run($query, $params);
		$query = "MATCH (p:Person) WHERE p.name = {name} RETURN p";
		$result = $this->driver->run($query, $params);
		$this->assertEquals(1,count($result));
		$this->assertArrayHasKey("p", $result[0]);
		$node = $result[0]["p"];
		$this->assertEquals(["id", "labels", "name"], array_keys($node));
	}

	public function testRunsQueryWithReturnAll()
	{
		$query = "CREATE (p:Person) SET p.name = {name}";
		$params = ["name" => "testy mctesty"];
		$this->driver->run($query, $params);
		$query = "MATCH (p:Person) WHERE p.name = {name} RETURN *";
		$result = $this->driver->run($query, $params);
		$this->assertEquals(1,count($result));
		$this->assertArrayHasKey("p", $result[0]);
		$node = $result[0]["p"];
		$this->assertEquals(["id", "labels", "name"], array_keys($node));
	}

	public function testRunsQueryWithMixedReturns()
	{
		$query = "CREATE (p:Person) SET p.name = {name} RETURN p";
		$params = ["name" => "testy mctesty"];
		$this->driver->run($query, $params);
		$query = "MATCH (p:Person) WHERE p.name = {name} RETURN p, count(p) as cnt";
		$result = $this->driver->run($query, $params);
		$this->assertEquals(1,count($result));
		$this->assertArrayHasKey("p", $result[0]);
		$this->assertArrayHasKey("cnt", $result[0]);
		$node = $result[0]["p"];
		$this->assertEquals(["id", "labels", "name"], array_keys($node));
	}
}