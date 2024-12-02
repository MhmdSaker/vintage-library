<?php
// Prevent any output before headers
ob_start();

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error.log');

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Clear any previous output
ob_clean();

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

        // Set default values for optional fields
        $newBook['imageUrl'] = $newBook['imageUrl'] ?? 'src/images/default-book.jpg';
        $newBook['copiesAvailable'] = intval($newBook['copiesAvailable'] ?? 1);
        $newBook['rating'] = 0;
        $newBook['reviews'] = [];
        $newBook['dateAdded'] = date('Y-m-d H:i:s');

        // Read existing books
        $booksPath = __DIR__ . '/../data/books.json';
        if (!file_exists($booksPath)) {
            // Create new books file if it doesn't exist
            file_put_contents($booksPath, json_encode(['books' => []]));
        }

        $booksJson = file_get_contents($booksPath);
        if ($booksJson === false) {
            sendError('Failed to read books database');
        }

        $books = json_decode($booksJson, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            sendError('Invalid books database format');
        }

        // Check for duplicate ISBN
        foreach ($books['books'] as $book) {
            if ($book['isbn'] === $newBook['isbn']) {
                sendError('A book with this ISBN already exists', 400);
            }
        }

        // Generate new ID
        $lastId = !empty($books['books']) ? end($books['books'])['id'] : '0';
        $newBook['id'] = strval(intval($lastId) + 1);

        // Add new book to array
        $books['books'][] = $newBook;

        // Write back to file with pretty print
        $success = file_put_contents($booksPath, json_encode($books, JSON_PRETTY_PRINT));
        if ($success === false) {
            sendError('Failed to save book');
        }

        // Return success response
        echo json_encode([
            'success' => true,
            'message' => 'Book added successfully',
            'book' => $newBook
        ]);
    } else {
        sendError('Method not allowed', 405);
    }
} catch (Exception $e) {
    error_log($e->getMessage());
    sendError('Server error: ' . $e->getMessage());
}

// Flush and end output buffering
ob_end_flush();
?> 