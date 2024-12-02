<?php
ob_start();

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error.log');

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

ob_clean();

function sendError($message, $code = 500) {
    http_response_code($code);
    echo json_encode(['success' => false, 'message' => $message]);
    exit;
}

function removeFromFile($filePath, $bookId) {
    if (file_exists($filePath)) {
        $jsonData = file_get_contents($filePath);
        $data = json_decode($jsonData, true);
        
        if (isset($data['books'])) {
            $data['books'] = array_filter($data['books'], function($item) use ($bookId) {
                return $item['id'] !== $bookId;
            });
            $data['books'] = array_values($data['books']);
            file_put_contents($filePath, json_encode($data, JSON_PRETTY_PRINT));
        }
    }
}

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $jsonData = file_get_contents('php://input');
        if (!$jsonData) {
            sendError('No data received');
        }

        $data = json_decode($jsonData, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            sendError('Invalid JSON data: ' . json_last_error_msg());
        }

        if (!isset($data['bookId'])) {
            sendError('Book ID is required', 400);
        }

        // Remove from books.json
        $booksPath = __DIR__ . '/../data/books.json';
        if (!file_exists($booksPath)) {
            sendError('Books database not found');
        }

        $booksJson = file_get_contents($booksPath);
        if ($booksJson === false) {
            sendError('Failed to read books database');
        }

        $books = json_decode($booksJson, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            sendError('Invalid books database format');
        }

        // Find and remove the book
        $bookFound = false;
        $books['books'] = array_filter($books['books'], function($book) use ($data, &$bookFound) {
            if ($book['id'] === $data['bookId']) {
                $bookFound = true;
                return false;
            }
            return true;
        });

        if (!$bookFound) {
            sendError('Book not found', 404);
        }

        // Reindex array
        $books['books'] = array_values($books['books']);

        // Write back to books.json
        $success = file_put_contents($booksPath, json_encode($books, JSON_PRETTY_PRINT));
        if ($success === false) {
            sendError('Failed to update books database');
        }

        // Also remove from favorites.json if it exists
        $favoritesPath = __DIR__ . '/../data/favorites.json';
        removeFromFile($favoritesPath, $data['bookId']);

        // Also remove from reading-list.json if it exists
        $readingListPath = __DIR__ . '/../data/reading-list.json';
        removeFromFile($readingListPath, $data['bookId']);

        echo json_encode([
            'success' => true,
            'message' => 'Book removed successfully'
        ]);
    } else {
        sendError('Method not allowed', 405);
    }
} catch (Exception $e) {
    error_log($e->getMessage());
    sendError('Server error: ' . $e->getMessage());
}

ob_end_flush();
?> 