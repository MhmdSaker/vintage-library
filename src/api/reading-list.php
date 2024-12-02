<?php
header('Content-Type: application/json');

// Get the request method
$method = $_SERVER['REQUEST_METHOD'];

// Get JSON data from request body
$json = file_get_contents('php://input');
$data = json_decode($json, true);

// File to store reading list
$file = '../data/reading-list.json';

// Ensure the file exists
if (!file_exists($file)) {
    file_put_contents($file, json_encode(['books' => []]));
}

// Read current reading list
$reading_list = json_decode(file_get_contents($file), true);

switch ($method) {
    case 'GET':
        // Return the reading list
        echo json_encode([
            'success' => true,
            'books' => $reading_list['books']
        ]);
        break;

    case 'POST':
        if (!isset($data['action'])) {
            echo json_encode([
                'success' => false,
                'message' => 'Action not specified'
            ]);
            exit;
        }

        switch ($data['action']) {
            case 'add':
                // Check if book already exists in reading list
                $exists = false;
                foreach ($reading_list['books'] as $book) {
                    if ($book['id'] === $data['bookId']) {
                        $exists = true;
                        break;
                    }
                }

                if ($exists) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Book already in reading list'
                    ]);
                    exit;
                }

                // Add new book
                $reading_list['books'][] = [
                    'id' => $data['bookId'],
                    'title' => $data['bookData']['title'],
                    'author' => $data['bookData']['author'],
                    'totalPages' => $data['bookData']['totalPages'],
                    'currentPage' => 0,
                    'completed' => false,
                    'dateAdded' => date('Y-m-d H:i:s')
                ];

                // Save to file
                file_put_contents($file, json_encode($reading_list));

                echo json_encode([
                    'success' => true,
                    'message' => 'Book added to reading list'
                ]);
                break;

            case 'remove':
                // Remove book from reading list
                $new_list = array_filter($reading_list['books'], function($book) use ($data) {
                    return $book['id'] !== $data['bookId'];
                });
                $reading_list['books'] = array_values($new_list);

                // Save to file
                file_put_contents($file, json_encode($reading_list));

                echo json_encode([
                    'success' => true,
                    'message' => 'Book removed from reading list'
                ]);
                break;

            case 'update':
                // Update book progress
                foreach ($reading_list['books'] as &$book) {
                    if ($book['id'] === $data['bookId']) {
                        $book['currentPage'] = $data['currentPage'];
                        $book['completed'] = $data['completed'];
                        break;
                    }
                }

                // Save to file
                file_put_contents($file, json_encode($reading_list));

                echo json_encode([
                    'success' => true,
                    'message' => 'Progress updated'
                ]);
                break;

            default:
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid action'
                ]);
                break;
        }
        break;

    default:
        echo json_encode([
            'success' => false,
            'message' => 'Invalid request method'
        ]);
        break;
}
?> 