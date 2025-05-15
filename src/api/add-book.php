<?php
// Include database connection
require_once 'db_connect.php';

// Set CORS headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Error handling function
function sendError($message, $code = 500) {
    http_response_code($code);
    echo json_encode(['success' => false, 'message' => $message]);
    exit;
}

try {
    // Check if it's a POST request
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Get JSON data from the request
        $jsonData = file_get_contents('php://input');
        if (!$jsonData) {
            sendError('No data received');
        }

        $newBook = json_decode($jsonData, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            sendError('Invalid JSON data: ' . json_last_error_msg());
        }

        // Validate required fields
        $requiredFields = ['title', 'author', 'isbn', 'genre', 'description'];
        foreach ($requiredFields as $field) {
            if (empty($newBook[$field])) {
                sendError("Missing required field: $field", 400);
            }
        }

        // Get database connection
        $conn = connectDB();
        if (!$conn) {
            sendError("Database connection failed");
        }

        // Check for duplicate ISBN
        $stmt = $conn->prepare("SELECT id FROM books WHERE isbn = :isbn");
        $stmt->bindParam(':isbn', $newBook['isbn']);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            sendError('A book with this ISBN already exists', 400);
        }

        // Set default values for optional fields
        $imageUrl = $newBook['imageUrl'] ?? 'src/images/default-book.jpg';
        $copiesAvailable = intval($newBook['copiesAvailable'] ?? 1);
        $publishDate = $newBook['publicationDate'] ?? date('Y');
        $language = $newBook['language'] ?? 'English';
        $format = $newBook['format'] ?? 'Hardcover';
        $condition = $newBook['condition'] ?? 'Good';
        $isFavorite = 0; // Default is not a favorite
        
        // Prepare SQL statement
        $stmt = $conn->prepare("INSERT INTO books (
            title, author, genre, publish_date, image_url, copies_available, 
            description, `condition`, language, format, is_favorite, isbn
        ) VALUES (
            :title, :author, :genre, :publish_date, :image_url, :copies_available,
            :description, :condition, :language, :format, :is_favorite, :isbn
        )");
        
        // Bind parameters
        $stmt->bindParam(':title', $newBook['title']);
        $stmt->bindParam(':author', $newBook['author']);
        $stmt->bindParam(':genre', $newBook['genre']);
        $stmt->bindParam(':publish_date', $publishDate);
        $stmt->bindParam(':image_url', $imageUrl);
        $stmt->bindParam(':copies_available', $copiesAvailable);
        $stmt->bindParam(':description', $newBook['description']);
        $stmt->bindParam(':condition', $condition);
        $stmt->bindParam(':language', $language);
        $stmt->bindParam(':format', $format);
        $stmt->bindParam(':is_favorite', $isFavorite, PDO::PARAM_BOOL);
        $stmt->bindParam(':isbn', $newBook['isbn']);
        
        // Execute the statement
        $result = $stmt->execute();
        
        if (!$result) {
            sendError('Failed to add book to database');
        }
        
        // Get the new book ID
        $bookId = $conn->lastInsertId();
        
        // Return success response
        http_response_code(201); // Created
        echo json_encode([
            'success' => true,
            'message' => 'Book added successfully',
            'book' => [
                'id' => $bookId,
                'title' => $newBook['title'],
                'author' => $newBook['author']
            ]
        ]);
    } else {
        sendError('Method not allowed', 405);
    }
} catch (PDOException $e) {
    error_log($e->getMessage());
    sendError('Database error: ' . $e->getMessage());
} catch (Exception $e) {
    error_log($e->getMessage());
    sendError('Server error: ' . $e->getMessage());
}
?>