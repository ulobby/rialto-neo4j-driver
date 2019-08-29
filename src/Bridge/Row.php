<?php

namespace Neo4jBridge\Bridge;

class Row implements \Countable, \Iterator, \ArrayAccess {

	protected $raw = null;
	protected $data = null;
	protected $columns = null;
	protected $position = 0;
	
	public function __construct(array $columns, array $rowData) {
		$this->columns = $columns;
		$this->data = [];
		$this->raw = array_values($rowData);
	}


	// ArrayAccess API

	public function offsetExists($offset)
	{
		if (!is_integer($offset)) {

			$rawOffset = array_search($offset, $this->columns);

			if ($rawOffset === false) {
				return false;
			}

			return isset($this->raw[$rawOffset]);
		}

		return isset($this->raw[$offset]);
	}

	public function offsetGet($offset)
	{
		if (!is_integer($offset)) {
			$offset = array_search($offset, $this->columns);
		}

		if (!isset($this->data[$offset])) {
			$data = $this->raw[$offset];
			if (is_array($data)) {
				$data = new Row(array_keys($data), array_values($data));
			}
			$this->data[$offset] = $data;
		}

		return $this->data[$offset];
	}

	public function offsetSet($offset, $value)
	{
		throw new \BadMethodCallException("You cannot modify a result row.");
	}

	public function offsetUnset($offset)
	{
		throw new \BadMethodCallException("You cannot modify a result row.");
	}


	// Countable API

	public function count()
	{
		return count($this->raw);
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
		return $this->columns[$this->position];
	}

	public function next()
	{
		++$this->position;
	}

	public function valid()
	{
		return $this->position < count($this->raw);
	}
}