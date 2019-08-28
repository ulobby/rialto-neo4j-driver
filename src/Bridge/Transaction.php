<?php

namespace Neo4jBridge\Bridge;

class Transaction
{
	private $write;

	public function __construct(Client $client, $write=true)
	{
		$this->write = $write;
	}

	public function commit()
	{
		if ($this->write) {
			$result = $this->client->runWriteTransaction($this);
		} else {
			$result = $this->client->runReadTransaction($this);
		}
		return $result;
	}

	public function rollback()
	{
		return;
	}
}