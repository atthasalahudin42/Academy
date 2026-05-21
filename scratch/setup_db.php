<?php
$host = "localhost";
$user = "root";
$pass = "";

try {
    $conn = new mysqli($host, $user, $pass);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "CREATE DATABASE IF NOT EXISTS academy";
    if ($conn->query($sql) === TRUE) {
        echo "Database 'academy' checked/created successfully.\n";
    } else {
        echo "Error creating database: " . $conn->error . "\n";
    }

    $conn->select_db("academy");
    
    // Read init_db.sql
    $sql_file = __DIR__ . '/../init_db.sql';
    if (file_exists($sql_file)) {
        $query = file_get_contents($sql_file);
        if ($conn->multi_query($query)) {
            echo "Tables initialized successfully.\n";
        } else {
            echo "Error initializing tables: " . $conn->error . "\n";
        }
    }

    $conn->close();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
