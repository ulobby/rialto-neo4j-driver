<?php

namespace Neo4jBridge\Bridge;

use Neo4jBridge\Neo4jBridge;

class Client
{
	private $bridge;
	private $entityMapper;

	public function __construct(\Neo4jBridge\Neo4jBridge $bridge)
	{
		$this->bridge = $bridge;
	}

	public function beginTransaction(): Transaction
	{
		return new Transaction($this);
	}

	public function executeCypherQuery(CypherQuery $query): ResultSet
	{
		//TODO map the rows to entities
		$raw = $this->bridge->run($query->getQuery(), $query->getParameters());
		$this->entityMapper = new EntityMapper($query); 
		$results = new ResultSet($this, ['data' => $raw, 'columns' => $query->getExpectedColumns()]);
		return $results;
	}

	public function getEntityMapper()
	{
		return $this->$entityMapper;
	}

	public function runWriteTransaction(Transaction $transaction): ResultSet
	{

	}

	public function runReadTransaction(Transaction $transaction): ResultSet
	{

	}
}