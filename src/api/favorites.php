<?php
header('Content-Type: application/json');
session_start();

// Initialize favorites array in session if it doesn't exist
if (!isset($_SESSION['favorites'])) {
    $_SESSION['favorites'] = [];
}

// Handle POST request (add/remove favorite)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $bookId = $data['bookId'] ?? null;
    $action = $data['action'] ?? 'toggle';
    $bookData = $data['bookData'] ?? null;

    if (!$bookId) {
        echo json_encode(['success' => false, 'message' => 'Book ID is required']);
        exit;
    }

    $favorites = &$_SESSION['favorites'];
    $existingIndex = array_search($bookId, array_column($favorites, 'id'));

    if ($action === 'add' && $bookData) {
        if ($existingIndex === false) {
            $favorites[] = [
                'id' => $bookId,
                'title' => $bookData['title'],
                'author' => $bookData['author'],
                'imageUrl' => $bookData['imageUrl']
            ];
            echo json_encode(['success' => true, 'message' => 'Book added to favorites']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Book already in favorites']);
        }
    } elseif ($action === 'remove') {
        if ($existingIndex !== false) {
            array_splice($favorites, $existingIndex, 1);
            echo json_encode(['success' => true, 'message' => 'Book removed from favorites']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Book not found in favorites']);
        }
    } elseif ($action === 'toggle') {
        if ($existingIndex !== false) {
            array_splice($favorites, $existingIndex, 1);
            echo json_encode(['success' => true, 'message' => 'Book removed from favorites']);
        } elseif ($bookData) {
            $favorites[] = [
                'id' => $bookId,
                'title' => $bookData['title'],
                'author' => $bookData['author'],
                'imageUrl' => $bookData['imageUrl']
            ];
            echo json_encode(['success' => true, 'message' => 'Book added to favorites']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Book data required for adding to favorites']);
        }
    }
    exit;
}

// Handle GET request (get favorites)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    echo json_encode([
        'success' => true,
        'favorites' => $_SESSION['favorites']
    ]);
    exit;
}
?> 