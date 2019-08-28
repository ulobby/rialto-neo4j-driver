<?php

namespace Neo4jBridge\Bridge;

use Neo4jBridge\Neo4jBridge;

class Client
{
	private $bridge;

	public function __construct(\Neo4jBridge\Neo4jBridge $bridge)
	{
		$this->bridge = $bridge;
	}

	public function beginTransaction(): Transaction
	{

	}

	public function executeCypherQuery(CypherQuery $query): ResultSet
	{
		$results = new ResultSet($this->bridge->run($query->getQuery(), $query->getParameters()));
		return $results;
	}
}