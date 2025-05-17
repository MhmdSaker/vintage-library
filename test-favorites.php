<?php
// This is a simple test script to toggle the favorite status of a book

// Get book ID from query parameter
$bookId = isset($_GET['book']) ? $_GET['book'] : 1;

// Define the API URL
$apiUrl = 'http://localhost/vintage-library/src/api/favorites.php';

// Create cURL resource
$ch = curl_init();

// Set cURL options
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['bookId' => $bookId, 'action' => 'toggle']));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Execute the POST request
$response = curl_exec($ch);

// Check for errors
if (curl_errno($ch)) {
    echo 'Error: ' . curl_error($ch);
} else {
    echo 'API Response: ' . $response;
    
    // Check the database status after the operation
    try {
        // Database connection parameters - matching your existing connection
        $host = 'localhost';
        $dbname = 'vintage_library';
        $username = 'root';
        $password = 'mhmd090';
        
        // Create database connection
        $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Check the current status of the book
        $stmt = $conn->prepare("SELECT id, title, is_favorite FROM books WHERE id = ?");
        $stmt->execute([$bookId]);
        $book = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($book) {
            echo '<hr>';
            echo '<h3>Database Status After Operation:</h3>';
            echo '<pre>';
            echo 'Book ID: ' . $book['id'] . '<br>';
            echo 'Title: ' . $book['title'] . '<br>';
            echo 'is_favorite: ' . $book['is_favorite'] . ' (' . ($book['is_favorite'] ? 'true' : 'false') . ')';
            echo '</pre>';
        } else {
            echo '<hr>Book not found in database.';
        }
    } catch (PDOException $e) {
        echo '<hr>Database error: ' . $e->getMessage();
    }
}

// Close cURL resource
curl_close($ch);

// Add a button to toggle again for easy testing
echo '<hr><a href="test-favorites.php?book=' . $bookId . '">Toggle Again</a>';
echo '&nbsp;&nbsp;|&nbsp;&nbsp;';
echo '<a href="test-favorites.php?book=' . ($bookId + 1) . '">Test Next Book (ID ' . ($bookId + 1) . ')</a>';
?> 