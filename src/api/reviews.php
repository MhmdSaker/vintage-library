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
        // Check if a book ID is provided
        if (isset($_GET['book_id'])) {
            getReviewsByBook($conn, $_GET['book_id']);
        } else {
            getAllReviews($conn);
        }
        break;
    case 'POST':
        addReview($conn);
        break;
    case 'PUT':
        updateReview($conn);
        break;
    case 'DELETE':
        if (isset($_GET['id'])) {
            deleteReview($conn, $_GET['id']);
        } else {
            returnJSON(['error' => 'Review ID is required'], 400);
        }
        break;
    default:
        returnJSON(['error' => 'Invalid request method'], 405);
        break;
}

// Function to get all reviews
function getAllReviews($conn) {
    try {
        // Join the reviews and books tables to get book details
        $stmt = $conn->prepare("
            SELECT r.id, r.book_id, b.title, r.reviewer_name, 
                   r.rating, r.comment, r.review_date
            FROM reviews r
            JOIN books b ON r.book_id = b.id
            ORDER BY r.review_date DESC
        ");
        $stmt->execute();
        $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        returnJSON(['reviews' => $reviews]);
    } catch (PDOException $e) {
        returnJSON(['error' => 'Failed to get reviews: ' . $e->getMessage()], 500);
    }
}

// Function to get reviews for a specific book
function getReviewsByBook($conn, $bookId) {
    try {
        $stmt = $conn->prepare("
            SELECT r.id, r.book_id, r.reviewer_name, 
                   r.rating, r.comment, r.review_date
            FROM reviews r
            WHERE r.book_id = :book_id
            ORDER BY r.review_date DESC
        ");
        $stmt->bindParam(':book_id', $bookId);
        $stmt->execute();
        $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        returnJSON(['reviews' => $reviews]);
    } catch (PDOException $e) {
        returnJSON(['error' => 'Failed to get reviews: ' . $e->getMessage()], 500);
    }
}

// Function to add a new review
function addReview($conn) {
    try {
        // Get request body
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Validate required fields
        if (!isset($data['book_id']) || !isset($data['reviewer_name']) || !isset($data['rating'])) {
            returnJSON(['error' => 'Book ID, reviewer name, and rating are required'], 400);
        }
        
        // Check if the book exists
        $checkStmt = $conn->prepare("SELECT id FROM books WHERE id = :book_id");
        $checkStmt->bindParam(':book_id', $data['book_id']);
        $checkStmt->execute();
        
        if ($checkStmt->rowCount() === 0) {
            returnJSON(['error' => 'Book not found'], 404);
        }
        
        // Prepare SQL query
        $stmt = $conn->prepare("
            INSERT INTO reviews (book_id, reviewer_name, rating, comment) 
            VALUES (:book_id, :reviewer_name, :rating, :comment)
        ");
        
        // Bind parameters
        $stmt->bindParam(':book_id', $data['book_id']);
        $stmt->bindParam(':reviewer_name', $data['reviewer_name']);
        $stmt->bindParam(':rating', $data['rating']);
        
        // Set comment to empty string if not provided
        $comment = isset($data['comment']) ? $data['comment'] : '';
        $stmt->bindParam(':comment', $comment);
        
        // Execute query
        $stmt->execute();
        
        // Get the ID of the inserted review
        $reviewId = $conn->lastInsertId();
        
        returnJSON(['message' => 'Review added successfully', 'id' => $reviewId], 201);
    } catch (PDOException $e) {
        returnJSON(['error' => 'Failed to add review: ' . $e->getMessage()], 500);
    }
}

// Function to update a review
function updateReview($conn) {
    try {
        // Get request body
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Check if ID is provided
        if (!isset($data['id'])) {
            returnJSON(['error' => 'Review ID is required'], 400);
        }
        
        // Prepare SQL query
        $sql = "UPDATE reviews SET ";
        $params = [];
        
        // Add fields to update only if they are provided
        if (isset($data['reviewer_name'])) {
            $sql .= "reviewer_name = :reviewer_name, ";
            $params[':reviewer_name'] = $data['reviewer_name'];
        }
        
        if (isset($data['rating'])) {
            $sql .= "rating = :rating, ";
            $params[':rating'] = $data['rating'];
        }
        
        if (isset($data['comment'])) {
            $sql .= "comment = :comment, ";
            $params[':comment'] = $data['comment'];
        }
        
        // Remove trailing comma and space
        $sql = rtrim($sql, ", ");
        
        // Add WHERE clause
        $sql .= " WHERE id = :id";
        $params[':id'] = $data['id'];
        
        // Prepare and execute query
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        
        // Check if review was found and updated
        if ($stmt->rowCount() > 0) {
            returnJSON(['message' => 'Review updated successfully']);
        } else {
            returnJSON(['error' => 'Review not found or no changes made'], 404);
        }
    } catch (PDOException $e) {
        returnJSON(['error' => 'Failed to update review: ' . $e->getMessage()], 500);
    }
}

// Function to delete a review
function deleteReview($conn, $id) {
    try {
        $stmt = $conn->prepare("DELETE FROM reviews WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        // Check if review was found and deleted
        if ($stmt->rowCount() > 0) {
            returnJSON(['message' => 'Review deleted successfully']);
        } else {
            returnJSON(['error' => 'Review not found'], 404);
        }
    } catch (PDOException $e) {
        returnJSON(['error' => 'Failed to delete review: ' . $e->getMessage()], 500);
    }
}
?> 