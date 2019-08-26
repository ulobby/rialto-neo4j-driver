<?php

namespace App\Bridge;

class ResultSet implements \Countable
{
	private $data = [];

	public function count(): int
	{
		return count($this->data);
	}

	public function getColumns(): array
	{

	}
}