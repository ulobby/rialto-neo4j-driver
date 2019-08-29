<?php

namespace Neo4jBridge\Bridge;

class ResultSet implements \Countable, \Iterator, \ArrayAccess
{
	private $data = [];
	private $row = [];

	public function __construct(array $data, array $columns)
	{
		$this->data = $data;
		$this->columns = $columns;
	}

	public function getColumns(): array
	{
		return $this->columns;
	}

	// ArrayAccess API

	public function offsetExists($offset)
	{
		return isset($this->data[$offset]);
	}

	public function offsetGet($offset)
	{
		if (!isset($this->rows[$offset])) {
			$this->rows[$offset] = new Row($this->columns, $this->data[$offset]);
		}
		return $this->rows[$offset];
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