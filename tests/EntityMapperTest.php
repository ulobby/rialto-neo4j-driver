<?php

use PHPUnit\Framework\TestCase;
use Neo4jBridge\Bridge\CypherQuery as Query;
use Neo4jBridge\Bridge\EntityMapper;
use Neo4jBridge\Bridge\Client;
use Neo4jBridge\Bridge\Node;

class EntityMapperTest extends TestCase
{
	public function testMapsColumnsToNodes()
	{
		$client = Mockery::mock(Client::class);
		// Check that nodes are found
		$queryString = "MATCH (p:Person) RETURN p";
		$query = new Query($client, $queryString);
		$mapper = new EntityMapper($query);
		$entity = $mapper->getEntityFor("p", []);
		$this->assertInstanceOf(Node::class, $entity);
	}

	public function testMapsColumnsToRelationships()
	{
		$this->markTestIncomplete();
		// Check that relationships are found
		$queryString = "MATCH (p)-[r:RELATION]->() RETURN r";
	}

	public function testMapsMixedResultsCorrectly()
	{
		$this->markTestIncomplete();
		// Check that we can find mixed results
		$queryString = "MATCH (p:Person)-[r:RELATION]->() RETURN r, p";	
	}

	public function testMapsOtherToArrays()
	{
		$this->markTestIncomplete();
		// Check that we can find Other types of return values
		$queryString = "MATCH (per:Person)-[rel:RELATION]->() RETURN count(rel)";		
	}
}