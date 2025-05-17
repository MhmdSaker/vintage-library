<?php
// Script to check and fix database issues with favorites

// Database connection parameters
$host = 'localhost';
$dbname = 'vintage_library';
$username = 'root';
$password = 'mhmd090';

try {
    // Create database connection
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>Database Column Check</h2>";
    
    // Check the structure of the books table
    $stmt = $conn->prepare("DESCRIBE books");
    $stmt->execute();
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $isFavoriteFound = false;
    
    echo "<h3>Books Table Structure:</h3>";
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    
    foreach ($columns as $column) {
        echo "<tr>";
        foreach ($column as $key => $value) {
            echo "<td>" . htmlspecialchars($value ?? 'NULL') . "</td>";
        }
        echo "</tr>";
        
        if ($column['Field'] == 'is_favorite') {
            $isFavoriteFound = true;
            $favoriteColumnType = $column['Type'];
        }
    }
    echo "</table>";
    
    // Check if is_favorite column exists
    if (!$isFavoriteFound) {
        echo "<p style='color: red'>Error: is_favorite column not found in books table!</p>";
        echo "<p>Adding is_favorite column...</p>";
        
        $conn->exec("ALTER TABLE books ADD COLUMN is_favorite TINYINT(1) DEFAULT 0");
        
        echo "<p style='color: green'>is_favorite column added successfully.</p>";
    } else {
        echo "<p>is_favorite column found with type: $favoriteColumnType</p>";
        
        // If not the right type, modify it
        if ($favoriteColumnType != 'tinyint(1)') {
            echo "<p style='color: orange'>Warning: is_favorite column type is not tinyint(1).</p>";
            echo "<p>Updating column type...</p>";
            
            $conn->exec("ALTER TABLE books MODIFY COLUMN is_favorite TINYINT(1) DEFAULT 0");
            
            echo "<p style='color: green'>Column type updated successfully.</p>";
        }
    }
    
    // Reset all books to non-favorite for testing
    if (isset($_GET['reset'])) {
        echo "<h3>Resetting all favorites...</h3>";
        $stmt = $conn->prepare("UPDATE books SET is_favorite = 0");
        $stmt->execute();
        echo "<p style='color: green'>All books set to non-favorite.</p>";
    }
    
    // Check current favorite status of all books
    $stmt = $conn->prepare("SELECT id, title, is_favorite FROM books");
    $stmt->execute();
    $books = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>Current Book Favorites:</h3>";
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Title</th><th>is_favorite</th><th>Action</th></tr>";
    
    foreach ($books as $book) {
        echo "<tr>";
        echo "<td>" . $book['id'] . "</td>";
        echo "<td>" . htmlspecialchars($book['title']) . "</td>";
        echo "<td>" . $book['is_favorite'] . "</td>";
        echo "<td>";
        echo "<a href='test-favorites.php?book=" . $book['id'] . "'>Toggle Favorite</a>";
        echo "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
} catch (PDOException $e) {
    echo "<h2>Database Error:</h2>";
    echo "<p style='color: red'>" . $e->getMessage() . "</p>";
}

echo "<p><a href='fix-favorites.php?reset=1'>Reset All Favorites</a> | ";
echo "<a href='fix-favorites.php'>Refresh</a></p>";
?> 