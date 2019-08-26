<?php

namespace Bridge;

use App\Neo4jBridge;

class Client
{
	private $bridge;

	public function __construct(string $host, string $port, string $user, string $password)
	{
		$params = [
			"host" => $host,
			"port" => $port,
			"user" => $user,
			"password" => $password
		];
		$this->bridge = new Neo4jBridge($bridge);
	}

	public function beginTransaction(): Transaction
	{

	}

	public function executeCypherQuery(): ResultSet
	{

	}
}