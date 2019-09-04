<?php

namespace Neo4jBridge\Bridge;

class Node
{
	protected $client;
	protected $labels = [];
	protected $id = null;

	public static function get(Client $client, array $ids)
	{
		if (empty($array)) {
			throw new \Exception("Empty id array passed to Node::get", 1);	
		}
		$queryString = "MATCH (n) WHERE id(n) IN {ids} RETURN n";
		$query = new CypherQuery($client, $queryString);
		return $client->executeCypherQuery($query);
	}

	public function setClient(Client $client)
	{
		$this->client = $client;
	}

	public function getProperties()
	{
		return $this->properties;
	}

	public function setId(int $id)
	{
		$this->id = $id;
	}

	public function getId()
	{
		return $this->id;
	}

	public function addLabels(array $labels)
	{
		$this->labels = array_merge($this->labels, $labels);
	}

	public function getLabels()
	{
		return $this->labels;
	}

	public function getLabelString()
	{
		return implode(":", $this->getLabels());
	}

	public function getProperty($property)
	{
		return (isset($this->properties[$property])) ? $this->properties[$property] : null;
	}

	public function setProperties($properties)
	{
		foreach ($properties as $property => $value) {
			if ($property === "id") {
				$this->setId((int)$value);
				continue;
			}
			$this->setProperty($property, $value);
		}
		return $this;
	}
	
	public function setProperty($property, $value)
	{
		if ($value === null) {
			$this->removeProperty($property);
		} else {
			$this->properties[$property] = $value;
		}
		return $this;
	}

	public function save()
	{
		$parameters= [];
		$parameters['properties'] = $this->getProperties();
		if ($this->getId()) {
			$idn = $this->getId();
			$parameters['idn'] = $idn;
			$query = "MATCH (n) WHERE id(n) = {idn} SET n = {properties} RETURN n";
		} else {
			$labels = $this->getLabelString();
			$query = "CREATE (n{$labels}) SET n = {properties} RETURN n";
		}
		$queryObject = new CypherQuery($this->client, $query, $parameters);
		$this->client->executeCypherQuery($queryObject);
		return true;
	}
}