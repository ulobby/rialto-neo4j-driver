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
		$raw = $this->bridge->run($query->getQuery(), $query->getParameters());
		$this->entityMapper = new EntityMapper($query); 
		$results = new ResultSet($this, ['data' => $raw, 'columns' => $query->getExpectedColumns()]);
		return $results;
	}

	public function getEntityMapper()
	{
		return $this->entityMapper;
	}

	/**
	* Currently labels do not have their own class, so this simply returns the input
	 */
	public function makeLabel(string $label)
	{
		return $label;
	}

	public function getNode(?int $id=null)
	{
		$node = $this->makeNode();
		$node->setId($id);
		$this->loadNode($node);
		return $node;
	}

	/**
	 * Create a new node object bound to this client
	 *
	 * @param array $properties
	 * @return Node
	 */
	public function makeNode($properties=array())
	{
		$node = new Node();
		$node->setClient($this);
		return $node->setProperties($properties);
	}

	public function loadNode(Node $node)
	{
		return $node->load();	
	}

	public function runWriteTransaction(Transaction $transaction): ResultSet
	{

	}

	public function runReadTransaction(Transaction $transaction): ResultSet
	{

	}
}