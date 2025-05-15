<?php
// Database connection parameters
$host = 'localhost';
$dbname = 'vintage_library';
$username = 'root';
$password = 'mhmd090'; // Default XAMPP password is empty

// Establish connection
function connectDB() {
    global $host, $dbname, $username, $password;
    
    try {
        $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        // Set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch(PDOException $e) {
        echo json_encode(['error' => 'Connection failed: ' . $e->getMessage()]);
        return null;
    }
}

// Helper function to return JSON response
function returnJSON($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}
?> 