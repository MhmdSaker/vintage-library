<?php
// This is a super simple script to toggle book favorite status directly in the database
header('Content-Type: application/json');

// Get parameters
$bookId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$status = isset($_GET['status']) ? (int)$_GET['status'] : -1;

// Validate
if ($bookId <= 0) {
    echo json_encode(['success' => false, 'error' => 'Invalid book ID']);
    exit;
}

if ($status !== 0 && $status !== 1) {
    echo json_encode(['success' => false, 'error' => 'Invalid status value']);
    exit;
}

try {
    // Database connection parameters
    $host = 'localhost';
    $dbname = 'vintage_library';
    $username = 'root';
    $password = 'mhmd090';
    
    // Create database connection
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Direct query with no parameters
    $query = "UPDATE books SET is_favorite = $status WHERE id = $bookId";
    $result = $conn->exec($query);
    
    // Check result
    if ($result === false) {
        echo json_encode(['success' => false, 'error' => 'Failed to update database']);
        exit;
    }
    
    // Return success
    echo json_encode([
        'success' => true, 
        'message' => 'Book ' . ($status ? 'added to' : 'removed from') . ' favorites',
        'book_id' => $bookId,
        'new_status' => $status
    ]);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
}
?> 