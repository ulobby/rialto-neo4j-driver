<?php

namespace Neo4jBridge\Bridge;

class CypherQuery
{
	private $result;
	private $client;
	private $query;
	private $parameters;

	public function __construct(Client $client, string $query, array $parameters = array())
	{
		$this->client = $client;
		$this->query = $query;
		if (count($parameters) > 0) {
			$this->parameters = $parameters;
		} else {
			$this->parameters = null;
		}
	}

	public function getResultSet(): ResultSet
	{
		if ($this->result === null) {
			$this->result = $this->client->executeCypherQuery($this);
		}
		return $this->result;
	}

	public function getQuery()
	{
		return $this->query;
	}

	public function getParameters()
	{
		return $this->parameters;
	}

	/**
	 * Parse out an array of return columns expected from the query
	 * The query is assumed to follow the schema 
	 * [MATCHES, WHERES, WITHS and so ON]
	 * [columns defined as RETURN person, location.title AS title]
	 * [ORDER BY, LIMIT, SKIP]
	 * @return array array of columns
	 */
	public function parseColumnsFromQuery()
	{
		$startToken = "return";
		$endTokens = ["order by", "limit", "skip"];
		$text = $this->getQuery();
		$startPos = mb_strpos($text, $startToken);
		// Return an empty array if no columns
		if ($startPos === false) {
			return [];
		}
		// Find potential endpoints of the column definition
		$endPositions = [count($text)];
		foreach ($endTokens as $token) {
			$pos = mb_strpos($text, $token);
			if ($pos) {
				$endPositions[] = $pos;
			} 
		}
		// Split out the column definition removing the RETURN and any ORDER BY, etc, clauses
		$interval = min($endPositions) - $startPos;
		$columnDef = mb_substr($text, $startPos, $interval);
		$columns = explode(",", $columnDef);
		// Split out any aliased columns like 'p AS person'
		$columns = array_map(function($column) {
				$unaliased = explode($column, " as ");
				return $unaliased[count($unaliased)];
			}, $columns);
		return $columns;
	}
}