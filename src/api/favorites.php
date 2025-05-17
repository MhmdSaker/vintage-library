<?php
require_once 'db_connect.php';

// Set headers
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

// Create a debug log function
function debug_log($message) {
    $logFile = __DIR__ . '/favorites_debug.log';
    $timestamp = date('Y-m-d H:i:s');
    $log = "[{$timestamp}] {$message}\n";
    file_put_contents($logFile, $log, FILE_APPEND);
}

debug_log("========= Favorites API request started =========");
debug_log("Request method: " . $_SERVER['REQUEST_METHOD']);
debug_log("Raw POST data: " . file_get_contents('php://input'));

// Get database connection
$conn = connectDB();
if (!$conn) {
    debug_log("Database connection failed");
    returnJSON(['success' => false, 'error' => 'Database connection failed'], 500);
}

// Handle POST request (add/remove/toggle favorite)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $bookId = $data['bookId'] ?? null;
    $action = $data['action'] ?? 'toggle';
    
    debug_log("POST request - bookId: " . ($bookId ? $bookId : 'null') . ", action: $action");

    if (!$bookId) {
        debug_log("Error: Book ID is required");
        returnJSON(['success' => false, 'message' => 'Book ID is required'], 400);
    }

    try {
        // Check if the book exists
        $checkStmt = $conn->prepare("SELECT id, is_favorite FROM books WHERE id = :book_id");
        $checkStmt->bindParam(':book_id', $bookId);
        $checkStmt->execute();
        
        $book = $checkStmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$book) {
            debug_log("Error: Book not found with ID $bookId");
            returnJSON(['success' => false, 'message' => 'Book not found'], 404);
        }
        
        $isFavorite = (bool)$book['is_favorite'];
        debug_log("Current is_favorite status: " . ($isFavorite ? 'true' : 'false'));
        
        // Process action
        if ($action === 'add' || ($action === 'toggle' && !$isFavorite)) {
            debug_log("Setting is_favorite to 1 for book ID $bookId - DIRECT QUERY");
            // Use direct query with hardcoded values to bypass any parameter binding issues
            $stmt = $conn->prepare("UPDATE books SET is_favorite = 1 WHERE id = $bookId");
            $result = $stmt->execute();
            
            debug_log("SQL execution result: " . ($result ? "success" : "failed"));
            debug_log("Rows affected: " . $stmt->rowCount());
            
            // Double-check if it worked
            $checkStmt = $conn->prepare("SELECT is_favorite FROM books WHERE id = $bookId");
            $checkStmt->execute();
            $check = $checkStmt->fetch(PDO::FETCH_ASSOC);
            debug_log("After update, is_favorite = " . $check['is_favorite']);
            
            returnJSON(['success' => true, 'message' => 'Book added to favorites', 'new_status' => 1]);
        } elseif ($action === 'remove' || ($action === 'toggle' && $isFavorite)) {
            debug_log("Setting is_favorite to 0 for book ID $bookId - DIRECT QUERY");
            // Use direct query with hardcoded values to bypass any parameter binding issues
            $stmt = $conn->prepare("UPDATE books SET is_favorite = 0 WHERE id = $bookId");
            $result = $stmt->execute();
            
            debug_log("SQL execution result: " . ($result ? "success" : "failed"));
            debug_log("Rows affected: " . $stmt->rowCount());
            
            // Double-check if it worked
            $checkStmt = $conn->prepare("SELECT is_favorite FROM books WHERE id = $bookId");
            $checkStmt->execute();
            $check = $checkStmt->fetch(PDO::FETCH_ASSOC);
            debug_log("After update, is_favorite = " . $check['is_favorite']);
            
            returnJSON(['success' => true, 'message' => 'Book removed from favorites', 'new_status' => 0]);
        }
    } catch (PDOException $e) {
        debug_log("Database error: " . $e->getMessage());
        returnJSON(['success' => false, 'error' => 'Failed to update favorite status: ' . $e->getMessage()], 500);
    }
}

// Handle GET request (get favorites)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    debug_log("GET request - retrieving favorites");
    try {
        $stmt = $conn->prepare("
            SELECT id, title, author, image_url as imageUrl 
            FROM books 
            WHERE is_favorite = TRUE
            ORDER BY updated_at DESC
        ");
        $stmt->execute();
        $favorites = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        debug_log("Found " . count($favorites) . " favorites");
        returnJSON(['success' => true, 'favorites' => $favorites]);
    } catch (PDOException $e) {
        debug_log("Database error: " . $e->getMessage());
        returnJSON(['success' => false, 'error' => 'Failed to get favorites: ' . $e->getMessage()], 500);
    }
}

debug_log("========= Favorites API request ended =========");
?> 