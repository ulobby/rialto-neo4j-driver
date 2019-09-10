<?php

namespace Neo4jBridge\Bridge;

class Label
{
	private $name;

	public function __construct($name)
	{
		$this->name = $name;
	}

	public function getName()
	{
		return $this->name;
	}

	public function __toString(): string
	{
		return $this->name;
	}
}