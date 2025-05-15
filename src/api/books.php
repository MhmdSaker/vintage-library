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
        // Check if an ID is provided
        if (isset($_GET['id'])) {
            // Get a specific book
            getBook($conn, $_GET['id']);
        } else {
            // Get all books
            getBooks($conn);
        }
        break;
    case 'POST':
        // Add a new book
        addBook($conn);
        break;
    case 'PUT':
        // Update a book
        updateBook($conn);
        break;
    case 'DELETE':
        // Delete a book
        if (isset($_GET['id'])) {
            deleteBook($conn, $_GET['id']);
        } else {
            returnJSON(['error' => 'Book ID is required'], 400);
        }
        break;
    default:
        returnJSON(['error' => 'Invalid request method'], 405);
        break;
}

// Function to get all books
function getBooks($conn) {
    try {
        $stmt = $conn->prepare("SELECT * FROM books");
        $stmt->execute();
        $books = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        returnJSON(['success' => true, 'books' => $books]);
    } catch (PDOException $e) {
        returnJSON(['success' => false, 'error' => 'Failed to get books: ' . $e->getMessage()], 500);
    }
}

// Function to get a specific book
function getBook($conn, $id) {
    try {
        $stmt = $conn->prepare("SELECT * FROM books WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $book = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($book) {
            returnJSON(['success' => true, 'book' => $book]);
        } else {
            returnJSON(['success' => false, 'error' => 'Book not found'], 404);
        }
    } catch (PDOException $e) {
        returnJSON(['success' => false, 'error' => 'Failed to get book: ' . $e->getMessage()], 500);
    }
}

// Function to add a new book
function addBook($conn) {
    try {
        // Get request body
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Validate required fields
        if (!isset($data['title']) || !isset($data['author']) || !isset($data['genre'])) {
            returnJSON(['error' => 'Title, author, and genre are required'], 400);
        }
        
        // Prepare SQL query
        $stmt = $conn->prepare("INSERT INTO books (title, author, genre, publish_date, image_url, copies_available, 
                                description, `condition`, language, format, is_favorite) 
                                VALUES (:title, :author, :genre, :publish_date, :image_url, :copies_available, 
                                :description, :condition, :language, :format, :is_favorite)");
        
        // Bind parameters
        $stmt->bindParam(':title', $data['title']);
        $stmt->bindParam(':author', $data['author']);
        $stmt->bindParam(':genre', $data['genre']);
        $stmt->bindParam(':publish_date', $data['publishDate']);
        $stmt->bindParam(':image_url', $data['imageUrl']);
        $stmt->bindParam(':copies_available', $data['copiesAvailable']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':condition', $data['condition']);
        $stmt->bindParam(':language', $data['language']);
        $stmt->bindParam(':format', $data['format']);
        
        // Set default value for is_favorite if not provided
        $isFavorite = isset($data['isFavorite']) ? $data['isFavorite'] : false;
        $stmt->bindParam(':is_favorite', $isFavorite, PDO::PARAM_BOOL);
        
        // Execute query
        $stmt->execute();
        
        // Get the ID of the inserted book
        $bookId = $conn->lastInsertId();
        
        returnJSON(['message' => 'Book added successfully', 'id' => $bookId], 201);
    } catch (PDOException $e) {
        returnJSON(['error' => 'Failed to add book: ' . $e->getMessage()], 500);
    }
}

// Function to update a book
function updateBook($conn) {
    try {
        // Get request body
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Check if ID is provided
        if (!isset($data['id'])) {
            returnJSON(['error' => 'Book ID is required'], 400);
        }
        
        // Prepare SQL query
        $sql = "UPDATE books SET ";
        $params = [];
        
        // Add fields to update only if they are provided
        if (isset($data['title'])) {
            $sql .= "title = :title, ";
            $params[':title'] = $data['title'];
        }
        
        if (isset($data['author'])) {
            $sql .= "author = :author, ";
            $params[':author'] = $data['author'];
        }
        
        if (isset($data['genre'])) {
            $sql .= "genre = :genre, ";
            $params[':genre'] = $data['genre'];
        }
        
        if (isset($data['publishDate'])) {
            $sql .= "publish_date = :publish_date, ";
            $params[':publish_date'] = $data['publishDate'];
        }
        
        if (isset($data['imageUrl'])) {
            $sql .= "image_url = :image_url, ";
            $params[':image_url'] = $data['imageUrl'];
        }
        
        if (isset($data['copiesAvailable'])) {
            $sql .= "copies_available = :copies_available, ";
            $params[':copies_available'] = $data['copiesAvailable'];
        }
        
        if (isset($data['description'])) {
            $sql .= "description = :description, ";
            $params[':description'] = $data['description'];
        }
        
        if (isset($data['condition'])) {
            $sql .= "`condition` = :condition, ";
            $params[':condition'] = $data['condition'];
        }
        
        if (isset($data['language'])) {
            $sql .= "language = :language, ";
            $params[':language'] = $data['language'];
        }
        
        if (isset($data['format'])) {
            $sql .= "format = :format, ";
            $params[':format'] = $data['format'];
        }
        
        if (isset($data['isFavorite'])) {
            $sql .= "is_favorite = :is_favorite, ";
            $params[':is_favorite'] = $data['isFavorite'];
        }
        
        // Remove trailing comma and space
        $sql = rtrim($sql, ", ");
        
        // Add WHERE clause
        $sql .= " WHERE id = :id";
        $params[':id'] = $data['id'];
        
        // Prepare and execute query
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        
        // Check if book was found and updated
        if ($stmt->rowCount() > 0) {
            returnJSON(['message' => 'Book updated successfully']);
        } else {
            returnJSON(['error' => 'Book not found or no changes made'], 404);
        }
    } catch (PDOException $e) {
        returnJSON(['error' => 'Failed to update book: ' . $e->getMessage()], 500);
    }
}

// Function to delete a book
function deleteBook($conn, $id) {
    try {
        $stmt = $conn->prepare("DELETE FROM books WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        // Check if book was found and deleted
        if ($stmt->rowCount() > 0) {
            returnJSON(['message' => 'Book deleted successfully']);
        } else {
            returnJSON(['error' => 'Book not found'], 404);
        }
    } catch (PDOException $e) {
        returnJSON(['error' => 'Failed to delete book: ' . $e->getMessage()], 500);
    }
}
?> 