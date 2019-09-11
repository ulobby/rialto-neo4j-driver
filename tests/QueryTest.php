<?php

use PHPUnit\Framework\TestCase;
use Neo4jBridge\Bridge\Client;
use Neo4jBridge\Bridge\CypherQuery as Query;

class QueryTest extends TestCase
{
	public function testParsesExpectedColumns()
	{
		$client = Mockery::mock(Client::class);

		$queryString = "MATCH (p:Person) RETURN p";
		$query = new Query($client, $queryString);
		$this->assertEquals(['p'], $query->getExpectedColumns());

		$queryString = "MATCH (p:Person) CREATE (p2:Person) WITH p, p2 RETURN p, p2";
		$query = new Query($client, $queryString);
		$this->assertEquals(['p', 'p2'], $query->getExpectedColumns());

		$queryString = "MATCH (p:Person) CREATE (p2:Person) WITH p, p2 RETURN p, p2 ORDER BY p.name SKIP 10 LIMIT 30";
		$query = new Query($client, $queryString);
		$this->assertEquals(['p', 'p2'], $query->getExpectedColumns());

		$queryString = "CREATE (p:Person {name: 'test mctestyness'})";
		$query = new Query($client, $queryString);
		$this->assertEquals([], $query->getExpectedColumns());

		$queryString = "MATCH (p:Person) MATCH (l:Location) RETURN p AS simon, l AS location";
		$query = new Query($client, $queryString);
		$this->assertEquals(['simon', 'location'], $query->getExpectedColumns());

		$queryString = "MATCH (p:Person) MATCH (l:Location) RETURN p AS simon, l AS location ORDER BY simon.name SKIP 10 LIMIT 30";
		$query = new Query($client, $queryString);
		$this->assertEquals(['simon', 'location'], $query->getExpectedColumns());

		$queryString = "MATCH (p:Person) MATCH (l:Location) RETURN * ORDER BY simon.name SKIP 10 LIMIT 30";
		$query = new Query($client, $queryString);
		$this->assertEquals(['p', 'l'], $query->getExpectedColumns());
	}
}