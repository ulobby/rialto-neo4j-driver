<?php

use PHPUnit\Framework\TestCase;
use Neo4jBridge\Bridge\Client;
use Neo4jBridge\Bridge\Node;
use Neo4jBridge\Bridge\CypherQuery as Query;
use Neo4jBridge\Neo4jBridge;

class NodeTest extends TestCase
{
	public function setUp(): void
	{
		$params = [
			"host" => "instance0",
			"user" => "neo4j",
			"password" => "dev"
		];
		$this->driver = new Neo4jBridge($params);
		$this->client = new Client($this->driver);
	}

	public function tearDown(): void
	{
		Mockery::close();
		$this->driver->run("MATCH (n) DETACH DELETE n");
		$this->driver->close();
	}

	public function testSavesNodes()
	{
		$node = new Node();
		$node->setClient($this->client);

		$properties = ["name" => "simon"];
		$node->setProperties($properties);
		$node->save();

		$queryString = "MATCH (n) WHERE n.name = {name} RETURN count(n)";
		$query = new Query($this->client, $queryString, $properties);
		$results = $query->getResultSet();
		$this->assertEquals(1, $results[0]['count(n)']);
	}

	public function testSavedNodesGetId()
	{
		$node = new Node();
		$node->setClient($this->client);
		$this->assertNull($node->getId());
		$properties = ["name" => "simon"];
		$node->setProperties($properties);
		$node->save();
		$this->assertInternalType('int', $node->getId());
	}
}