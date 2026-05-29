<?php

use PHPUnit\Framework\TestCase;
use Dell7420\Academy\Database;

class DatabaseTest extends TestCase
{
    public function testDatabaseConnection()
    {
        $db = new Database();
        $conn = $db->connect();

        $this->assertNotNull($conn);
        $this->assertInstanceOf(\mysqli::class, $conn);
    }
}