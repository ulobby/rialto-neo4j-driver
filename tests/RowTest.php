<?php

use PHPUnit\Framework\TestCase;
use Neo4jBridge\Bridge\Client;
use Neo4jBridge\Bridge\CypherQuery as Query;
use Neo4jBridge\Bridge\Row;

class RowTest extends TestCase
{
	public function testsCanAccessRowWithKeys()
	{
		$columns = ["name", "location"];
		$data = ["name" => "mark", "location" => "Los Angeles"];
		$row = new Row($columns, $data);
		$this->assertTrue($row["name"] === "mark");
		$this->assertTrue($row["location"] ==="Los Angeles");
	}

	public function testsCanAccessRowWithIntegers()
	{
		$columns = ["name", "location"];
		$data = ["name" => "mark", "location" => "Los Angeles"];
		$row = new Row($columns, $data);
		$this->assertTrue($row[0] === "mark");
		$this->assertTrue($row[1] ==="Los Angeles");
	}

	public function testsCanCreateRowOfRows()
	{
		$columns = ["name", "location"];
		$data = ["name" => "mark", "location" => "Los Angeles"];
		$innerRow = new Row($columns, $data);
		$outerRow = new Row(["a"], ["a" => $innerRow]);
		$this->assertEquals($outerRow["a"], $innerRow);
	}
}