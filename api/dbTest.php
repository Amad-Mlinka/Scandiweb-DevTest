<?php

require_once 'Database.php';

class DBTest {
    public function testConnection() {
        try {
            $db = Database::getInstance()->getConnection();
            if ($db) {
                return "DB Connection Successful";
            } else {
                return "DB Connection Failed";
            }
        } catch (\PDOException $e) {
            return "DB Connection Failed: " . $e->getMessage();
        }
    }
}

// Quick test to check if everything is correrctly configured 
$test = new DBTest();
echo $test->testConnection();

?>