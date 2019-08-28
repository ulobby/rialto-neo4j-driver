<?php

namespace Neo4jBridge\Bridge;

class ResultSet implements \Countable, \Iterator, \ArrayAccess
{
	private $data = [];

	public function __construct(array $data)
	{
		$this->data = $data;
	}

	public function getColumns(): array
	{

	}

	// ArrayAccess API

	public function offsetExists($offset)
	{
		return isset($this->data[$offset]);
	}

	public function offsetGet($offset)
	{
		return $this->data[$offset];
	}

	public function offsetSet($offset, $value)
	{
		throw new \BadMethodCallException("You cannot modify a query result.");
	}

	public function offsetUnset($offset)
	{
		throw new \BadMethodCallException("You cannot modify a query result.");
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