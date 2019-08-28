<?php

namespace Neo4jBridge\Bridge;

class ResultSet implements \Countable, \Iterator
{
	private $data = [];

	public function __construct(array $data)
	{
		$this->data = $data;
	}

	public function count(): int
	{
		return count($this->data);
	}

	public function getColumns(): array
	{

	}
}