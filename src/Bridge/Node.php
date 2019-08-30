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
		$this->loadProperties();
		foreach ($properties as $property => $value) {
			$this->setProperty($property, $value);
		}
		return $this;
	}
	
	public function setProperty($property, $value)
	{
		$this->loadProperties();
		if ($value === null) {
			$this->removeProperty($property);
		} else {
			$this->properties[$property] = $value;
		}
		return $this;
	}

}