<?php

namespace Neo4jBridge\Bridge;

class ResultSet implements \Countable, \Iterator
{
	private $data = [];

	public function __construct(array $data)
	{
		$this->data = $data;
	}

	public function getColumns(): array
	{

	}

	// Countable API

	public function count(): int
	{
		return count($this->data);
	}

	// Iterator API

	public function rewind()
	{
		$this->position = 0;
	}

	public function current()
	{
		return $this[$this->position];
	}

	public function key()
	{
		return $this->position;
	}

	public function next()
	{
		++$this->position;
	}

	public function valid()
	{
		return isset($this->data[$this->position]);
	}
}