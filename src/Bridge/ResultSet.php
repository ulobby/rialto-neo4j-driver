<?php

namespace Neo4jBridge\Bridge;

class ResultSet implements \Countable, \Iterator, \ArrayAccess
{
	protected $client;
	protected $rows = array();
	protected $data = array();
	protected $columns = array();
	protected $position = 0;

	public function __construct(Client $client, array $raw)
	{
		$this->client = $client;
		$this->data = $raw['data'];
		$this->columns = $raw['columns'];
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
			$this->rows[$offset] = new Row($this->client, $this->columns, $this->data[$offset]);
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