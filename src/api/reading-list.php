<?php
require_once 'db_connect.php';

// Set headers for CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Get database connection
$conn = connectDB();
if (!$conn) {
    returnJSON(['error' => 'Database connection failed'], 500);
}

// Determine request method
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        getReadingList($conn);
        break;
    case 'POST':
        addToReadingList($conn);
        break;
    case 'PUT':
        updateReadingListItem($conn);
        break;
    case 'DELETE':
        if (isset($_GET['id'])) {
            removeFromReadingList($conn, $_GET['id']);
        } else {
            returnJSON(['success' => false, 'error' => 'Reading list item ID is required'], 400);
        }
        break;
    default:
        returnJSON(['success' => false, 'error' => 'Invalid request method'], 405);
        break;
}

// Function to get the reading list
function getReadingList($conn) {
    try {
        // Join the reading_list and books tables to get book details
        $stmt = $conn->prepare("
            SELECT rl.id, rl.book_id, b.title, b.author, rl.total_pages, 
                   rl.current_page, rl.completed, rl.date_added
            FROM reading_list rl
            JOIN books b ON rl.book_id = b.id
            ORDER BY rl.date_added DESC
        ");
        $stmt->execute();
        $readingList = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        returnJSON(['success' => true, 'books' => $readingList]);
    } catch (PDOException $e) {
        returnJSON(['success' => false, 'error' => 'Failed to get reading list: ' . $e->getMessage()], 500);
    }
}

// Function to add a book to the reading list
function addToReadingList($conn) {
    try {
        // Get request body
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Validate required fields
        if (!isset($data['book_id'])) {
            returnJSON(['success' => false, 'error' => 'Book ID is required'], 400);
        }
        
        // Check if the book exists
        $checkStmt = $conn->prepare("SELECT id FROM books WHERE id = :book_id");
        $checkStmt->bindParam(':book_id', $data['book_id']);
        $checkStmt->execute();
        
        if ($checkStmt->rowCount() === 0) {
            returnJSON(['success' => false, 'error' => 'Book not found'], 404);
        }
        
        // Check if the book is already in the reading list
        $existStmt = $conn->prepare("SELECT id FROM reading_list WHERE book_id = :book_id");
        $existStmt->bindParam(':book_id', $data['book_id']);
        $existStmt->execute();
        
        if ($existStmt->rowCount() > 0) {
            returnJSON(['success' => false, 'error' => 'Book is already in the reading list'], 400);
        }
        
        // Prepare SQL query
        $stmt = $conn->prepare("
            INSERT INTO reading_list (book_id, total_pages, current_page, completed) 
            VALUES (:book_id, :total_pages, :current_page, :completed)
        ");
        
        // Bind parameters
        $stmt->bindParam(':book_id', $data['book_id']);
        
        // Set default values if not provided
        $totalPages = isset($data['total_pages']) ? $data['total_pages'] : 1;
        $currentPage = isset($data['current_page']) ? $data['current_page'] : 0;
        $completed = isset($data['completed']) ? $data['completed'] : false;
        
        $stmt->bindParam(':total_pages', $totalPages);
        $stmt->bindParam(':current_page', $currentPage);
        $stmt->bindParam(':completed', $completed, PDO::PARAM_BOOL);
        
        // Execute query
        $stmt->execute();
        
        // Get the ID of the inserted item
        $itemId = $conn->lastInsertId();
        
        returnJSON(['success' => true, 'message' => 'Book added to reading list successfully', 'id' => $itemId], 201);
    } catch (PDOException $e) {
        returnJSON(['success' => false, 'error' => 'Failed to add book to reading list: ' . $e->getMessage()], 500);
    }
}

// Function to update a reading list item
function updateReadingListItem($conn) {
    try {
        // Get request body
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Check if ID is provided
        if (!isset($data['id'])) {
            returnJSON(['success' => false, 'error' => 'Reading list item ID is required'], 400);
        }
        
        // Get book details if marking as completed to set current_page to total_pages
        if (isset($data['completed']) && $data['completed'] && !isset($data['current_page'])) {
            $getBookStmt = $conn->prepare("SELECT total_pages FROM reading_list WHERE id = :id");
            $getBookStmt->bindParam(':id', $data['id']);
            $getBookStmt->execute();
            $book = $getBookStmt->fetch(PDO::FETCH_ASSOC);
            
            if ($book) {
                $data['current_page'] = $book['total_pages'];
            }
        }
        
        // Prepare SQL query
        $sql = "UPDATE reading_list SET ";
        $params = [];
        
        // Add fields to update only if they are provided
        if (isset($data['total_pages'])) {
            $sql .= "total_pages = :total_pages, ";
            $params[':total_pages'] = $data['total_pages'];
        }
        
        if (isset($data['current_page'])) {
            $sql .= "current_page = :current_page, ";
            $params[':current_page'] = $data['current_page'];
        }
        
        if (isset($data['completed'])) {
            $sql .= "completed = :completed, ";
            $params[':completed'] = $data['completed'];
        }
        
        // Remove trailing comma and space
        $sql = rtrim($sql, ", ");
        
        // Add WHERE clause
        $sql .= " WHERE id = :id";
        $params[':id'] = $data['id'];
        
        // Prepare and execute query
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        
        // Check if item was found and updated
        if ($stmt->rowCount() > 0) {
            returnJSON(['success' => true, 'message' => 'Reading list item updated successfully']);
        } else {
            returnJSON(['success' => false, 'error' => 'Reading list item not found or no changes made'], 404);
        }
    } catch (PDOException $e) {
        returnJSON(['success' => false, 'error' => 'Failed to update reading list item: ' . $e->getMessage()], 500);
    }
}

// Function to remove a book from the reading list
function removeFromReadingList($conn, $id) {
    try {
        $stmt = $conn->prepare("DELETE FROM reading_list WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        // Check if item was found and deleted
        if ($stmt->rowCount() > 0) {
            returnJSON(['success' => true, 'message' => 'Book removed from reading list successfully']);
        } else {
            returnJSON(['success' => false, 'error' => 'Reading list item not found'], 404);
        }
    } catch (PDOException $e) {
        returnJSON(['success' => false, 'error' => 'Failed to remove book from reading list: ' . $e->getMessage()], 500);
    }
}
?> 