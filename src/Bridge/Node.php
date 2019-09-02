<?php

namespace Neo4jBridge\Bridge;

class Node
{

	public function __construct()
	{
		return;
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

}