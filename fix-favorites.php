<?php
// Script to check and fix database issues with favorites and handle add/remove actions

// Database connection parameters
$host = 'localhost';
$dbname = 'vintage_library';
$username = 'root';
$password = 'mhmd090';

// Check if we have an action and book_id
$action = isset($_GET['action']) ? $_GET['action'] : null;
$book_id = isset($_GET['book_id']) ? (int)$_GET['book_id'] : 0;
$return_url = isset($_GET['return_url']) ? $_GET['return_url'] : 'index.php';

// Add query string to return_url if present
if (isset($_GET['return_url']) && isset($_SERVER['QUERY_STRING'])) {
    $query_string = $_SERVER['QUERY_STRING'];
    // Remove the parameters we're using for this script
    $query_string = preg_replace('/&?action=[^&]*/', '', $query_string);
    $query_string = preg_replace('/&?book_id=[^&]*/', '', $query_string);
    $query_string = preg_replace('/&?return_url=[^&]*/', '', $query_string);
    
    // If we still have a query string, add it to the return_url if it doesn't already have one
    if (!empty($query_string) && strpos($return_url, '?') === false) {
        $return_url .= '?' . $query_string;
    }
}

try {
    // Create database connection
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Handle add/remove actions
    if ($action && $book_id > 0) {
        if ($action === 'add') {
            // First check if the book exists
            $check_stmt = $conn->prepare("SELECT id FROM books WHERE id = ?");
            $check_stmt->execute([$book_id]);
            
            if ($check_stmt->rowCount() > 0) {
                // Check if it's already a favorite
                $fav_check = $conn->prepare("SELECT book_id FROM favorites WHERE book_id = ?");
                $fav_check->execute([$book_id]);
                
                if ($fav_check->rowCount() === 0) {
                    // Add to favorites table
                    $add_fav = $conn->prepare("INSERT INTO favorites (book_id) VALUES (?)");
                    $add_fav->execute([$book_id]);
                    
                    // Also update is_favorite in books table
                    $update_book = $conn->prepare("UPDATE books SET is_favorite = 1 WHERE id = ?");
                    $update_book->execute([$book_id]);
                    
                    // Set a message (could use session to display after redirect)
                    $message = "Book added to favorites";
                }
            }
        } elseif ($action === 'remove') {
            // Remove from favorites table
            $remove_fav = $conn->prepare("DELETE FROM favorites WHERE book_id = ?");
            $remove_fav->execute([$book_id]);
            
            // Update is_favorite in books table
            $update_book = $conn->prepare("UPDATE books SET is_favorite = 0 WHERE id = ?");
            $update_book->execute([$book_id]);
            
            // Set a message
            $message = "Book removed from favorites";
        }
        
        // Redirect back to the referring page
        header("Location: $return_url");
        exit;
    }
    
    // The rest of the script for checking database structure, etc.
    // Only runs if no action was specified or after processing an action
    
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
    
    // Check favorites table structure
    echo "<h3>Favorites Table:</h3>";
    try {
        $stmt = $conn->prepare("DESCRIBE favorites");
        $stmt->execute();
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        
        foreach ($columns as $column) {
            echo "<tr>";
            foreach ($column as $key => $value) {
                echo "<td>" . htmlspecialchars($value ?? 'NULL') . "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    } catch (PDOException $e) {
        echo "<p style='color: red'>Error: Favorites table not found or cannot be accessed.</p>";
        echo "<p>Creating favorites table...</p>";
        
        $conn->exec("CREATE TABLE IF NOT EXISTS favorites (
            id INT AUTO_INCREMENT PRIMARY KEY,
            book_id INT NOT NULL,
            date_added TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE
        )");
        
        echo "<p style='color: green'>Favorites table created successfully.</p>";
    }
    
    // Reset all books to non-favorite for testing
    if (isset($_GET['reset'])) {
        echo "<h3>Resetting all favorites...</h3>";
        
        // Clear favorites table
        $stmt = $conn->prepare("DELETE FROM favorites");
        $stmt->execute();
        
        // Reset is_favorite in books table
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
    echo "<tr><th>ID</th><th>Title</th><th>is_favorite</th><th>In Favorites Table</th><th>Action</th></tr>";
    
    foreach ($books as $book) {
        // Check if book is in favorites table
        $fav_check = $conn->prepare("SELECT book_id FROM favorites WHERE book_id = ?");
        $fav_check->execute([$book['id']]);
        $in_favorites = $fav_check->rowCount() > 0 ? 'Yes' : 'No';
        
        // Check if is_favorite is inconsistent with favorites table
        $is_inconsistent = ($book['is_favorite'] == 1 && $in_favorites == 'No') || 
                          ($book['is_favorite'] == 0 && $in_favorites == 'Yes');
        
        echo "<tr" . ($is_inconsistent ? " style='background-color: #ffe6e6;'" : "") . ">";
        echo "<td>" . $book['id'] . "</td>";
        echo "<td>" . htmlspecialchars($book['title']) . "</td>";
        echo "<td>" . $book['is_favorite'] . "</td>";
        echo "<td>" . $in_favorites . "</td>";
        echo "<td>";
        echo "<a href='fix-favorites.php?action=" . ($book['is_favorite'] ? "remove" : "add") . "&book_id=" . $book['id'] . "'>Toggle Favorite</a>";
        if ($is_inconsistent) {
            echo " | <a href='fix-favorites.php?action=fix&book_id=" . $book['id'] . "' style='color: red;'>Fix</a>";
        }
        echo "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
} catch (PDOException $e) {
    echo "<h2>Database Error:</h2>";
    echo "<p style='color: red'>" . $e->getMessage() . "</p>";
}

echo "<p><a href='fix-favorites.php?reset=1'>Reset All Favorites</a> | ";
echo "<a href='index.php'>Back to Home</a> | ";
echo "<a href='fix-favorites.php'>Refresh</a></p>";
?> 