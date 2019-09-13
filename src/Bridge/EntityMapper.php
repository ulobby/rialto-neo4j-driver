<?php

namespace Neo4jBridge\Bridge;

use Neo4jBridge\Bridge\Node;

class EntityMapper
{
	protected $mapping;

	public function __construct(CypherQuery $query)
	{
		$this->mapping = $this->getColumnToEntityMapping($query->getQuery(), $query->getExpectedColumns());
	}

	/**
	 * Parse out the types of expected results in each row
	 * which can be relationships or nodes. Anything
	 * not either will be returned as a "normal" value.
	 * Does not work on aliased returns values.
	 */
	protected function getColumnToEntityMapping(string $query, array $columns)
	{
		$mapping = array();
		foreach ($columns as $column) {
			$escapedColumn = preg_quote($column);
			$nodePattern = '/(?:\()' . $escapedColumn. '(?::[\w`]*)*(?:\))/';
			$relPattern = '/(?:\[)' . $escapedColumn. '(?::?[\w`]*\])/';
			$isNode = preg_match($nodePattern, $query);
			$isRel = preg_match($relPattern, $query);
			if ($isNode && $isRel) {
				throw new \Exception("Variable found to be both node and relationship in $query", 1);
			}
			if ($isNode) {
				$mapping[$column] = "node";
			} elseif ($isRel) {
				$mapping[$column] = "relationship";
			} else {
				$mapping[$column] = "other";
			}
		}
		return $mapping;
	}

	public function getEntityFor(string $key, $raw)
	{
		$type = isset($this->mapping[$key]) ? $this->mapping[$key] : null;
		if ($type === "node") {
			$node = new Node();
			$node->setProperties($raw);
			return $node;
		}
		return $raw;
	}

}