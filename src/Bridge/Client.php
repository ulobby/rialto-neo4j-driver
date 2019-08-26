<?php

namespace App\Bridge;

use App\Neo4jBridge;

class Client
{
	private $bridge;

	public function __construct(\App\Neo4jBridge $bridge)
	{
		$this->bridge = $bridge;
	}

	public function beginTransaction(): Transaction
	{

	}

	public function executeCypherQuery(): ResultSet
	{

	}
}