<?php

use PHPUnit\Framework\TestCase;
use App\Neo4jConnection;

class Neo4jConnectionTest extends TestCase
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

	public function testRunsQuery()
	{
		$query = "CREATE (p:Person) SET p.name = {name} RETURN p";
		$params = ["name" => "testy mctesty"];
		$this->driver->run($query, $params);
		$query = "MATCH (p:Person) WHERE p.name = {name} RETURN p";
		$result = $this->driver->run($query, $params);
		var_dump($result);
	}
}