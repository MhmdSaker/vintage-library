<?php
// This is a super simple script to toggle book favorite status directly in the database
header('Content-Type: application/json');

// Get parameters
$bookId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$status = isset($_GET['status']) ? (int)$_GET['status'] : -1;
$return_url = isset($_GET['return_url']) ? $_GET['return_url'] : null;

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
    
    // Begin transaction to ensure consistency between tables
    $conn->beginTransaction();
    
    try {
        // Update is_favorite in books table
        $update_book = $conn->prepare("UPDATE books SET is_favorite = :status WHERE id = :id");
        $update_book->bindParam(':status', $status, PDO::PARAM_INT);
        $update_book->bindParam(':id', $bookId, PDO::PARAM_INT);
        $update_book->execute();
        
        // Check if the book exists in favorites table
        $check_favorite = $conn->prepare("SELECT book_id FROM favorites WHERE book_id = :id");
        $check_favorite->bindParam(':id', $bookId, PDO::PARAM_INT);
        $check_favorite->execute();
        $exists = $check_favorite->rowCount() > 0;
        
        if ($status === 1 && !$exists) {
            // Add to favorites if status=1 and not already in favorites
            $add_favorite = $conn->prepare("INSERT INTO favorites (book_id) VALUES (:id)");
            $add_favorite->bindParam(':id', $bookId, PDO::PARAM_INT);
            $add_favorite->execute();
        } elseif ($status === 0 && $exists) {
            // Remove from favorites if status=0 and in favorites
            $remove_favorite = $conn->prepare("DELETE FROM favorites WHERE book_id = :id");
            $remove_favorite->bindParam(':id', $bookId, PDO::PARAM_INT);
            $remove_favorite->execute();
        }
        
        // Commit the transaction
        $conn->commit();
        
        // If a return URL was provided, redirect to it (for direct browser access)
        if ($return_url) {
            header("Location: $return_url");
            exit;
        }
        
        // Return success for API calls
        echo json_encode([
            'success' => true, 
            'message' => 'Book ' . ($status ? 'added to' : 'removed from') . ' favorites',
            'book_id' => $bookId,
            'new_status' => $status
        ]);
        
    } catch (Exception $e) {
        // Rollback the transaction if anything failed
        $conn->rollback();
        echo json_encode(['success' => false, 'error' => 'Transaction failed: ' . $e->getMessage()]);
    }
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
}
?> 