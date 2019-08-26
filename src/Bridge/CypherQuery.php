<?php

namespace Bridge;

class CypherQuery
{
	private $result;
	private $client;
	private $query;
	private $parameters;

	public function __construct(Client $client, string $query, array $parameters)
	{
		$this->client = $client;
		$this->query = $query;
		$this->parameters = $parameters;
	}
	public function getResultSet(): ResultSet
	{
	public function getQuery()
	{
		return $this->query;
	}

	public function getParameters()
	{
		return $this->parameters;
	}
}