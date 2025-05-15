<?php
/**
 * Database Migration Script
 * 
 * This script migrates data from JSON files to MySQL database.
 * Run this script once to set up the database with existing data.
 */

// Include database connection
require_once 'src/api/db_connect.php';

// Set up output formatting
echo "<style>
    body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }
    h1, h2 { color: #8b7355; }
    .success { color: green; }
    .error { color: red; }
    pre { background: #f5f5f5; padding: 10px; border-radius: 5px; overflow-x: auto; }
</style>";

echo "<h1>Vintage Library - Database Migration</h1>";

// Connect to database
$conn = connectDB();
if (!$conn) {
    die("<p class='error'>Database connection failed. Please check your database settings.</p>");
}

echo "<p>Connected to database successfully.</p>";

// Function to create the database schema
function createDatabaseSchema($conn) {
    echo "<h2>Creating Database Schema</h2>";
    
    try {
        // Read the SQL file
        $sqlFile = file_get_contents('database.sql');
        
        // Split into separate queries
        $queries = explode(';', $sqlFile);
        
        // Execute each query
        foreach ($queries as $query) {
            $query = trim($query);
            if (empty($query)) continue;
            
            $conn->exec($query);
            echo "<p class='success'>Executed query successfully.</p>";
        }
        
        echo "<p class='success'>Database schema created successfully.</p>";
        return true;
    } catch (PDOException $e) {
        echo "<p class='error'>Error creating database schema: " . $e->getMessage() . "</p>";
        return false;
    }
}

// Function to migrate books from JSON to MySQL
function migrateBooks($conn) {
    echo "<h2>Migrating Books</h2>";
    
    try {
        // Check if the books table already has data
        $stmt = $conn->query("SELECT COUNT(*) FROM books");
        $count = $stmt->fetchColumn();
        
        if ($count > 0) {
            echo "<p>Books table already has data. Skipping migration.</p>";
            return true;
        }
        
        // Read JSON file
        $jsonFile = 'src/data/books.json';
        if (!file_exists($jsonFile)) {
            echo "<p class='error'>Books JSON file not found.</p>";
            return false;
        }
        
        $booksData = json_decode(file_get_contents($jsonFile), true);
        
        if (!isset($booksData['books']) || empty($booksData['books'])) {
            echo "<p class='error'>No books found in JSON file.</p>";
            return false;
        }
        
        // Prepare insert statement
        $stmt = $conn->prepare("
            INSERT INTO books (id, title, author, genre, publish_date, image_url, 
                              copies_available, description, `condition`, language, 
                              format, is_favorite)
            VALUES (:id, :title, :author, :genre, :publish_date, :image_url, 
                   :copies_available, :description, :condition, :language, 
                   :format, :is_favorite)
        ");
        
        // Insert each book
        $count = 0;
        foreach ($booksData['books'] as $book) {
            $params = [
                ':id' => $book['id'],
                ':title' => $book['title'],
                ':author' => $book['author'],
                ':genre' => $book['genre'],
                ':publish_date' => $book['publishDate'] ?? null,
                ':image_url' => $book['imageUrl'] ?? null,
                ':copies_available' => $book['copiesAvailable'] ?? 1,
                ':description' => $book['description'] ?? null,
                ':condition' => $book['condition'] ?? null,
                ':language' => $book['language'] ?? null,
                ':format' => $book['format'] ?? null,
                ':is_favorite' => $book['isFavorite'] ?? false
            ];
            
            $stmt->execute($params);
            $count++;
        }
        
        echo "<p class='success'>Migrated $count books successfully.</p>";
        return true;
    } catch (PDOException $e) {
        echo "<p class='error'>Error migrating books: " . $e->getMessage() . "</p>";
        return false;
    }
}

// Function to migrate reading list from JSON to MySQL
function migrateReadingList($conn) {
    echo "<h2>Migrating Reading List</h2>";
    
    try {
        // Check if the reading_list table already has data
        $stmt = $conn->query("SELECT COUNT(*) FROM reading_list");
        $count = $stmt->fetchColumn();
        
        if ($count > 0) {
            echo "<p>Reading list table already has data. Skipping migration.</p>";
            return true;
        }
        
        // Read JSON file
        $jsonFile = 'src/data/reading-list.json';
        if (!file_exists($jsonFile)) {
            echo "<p class='error'>Reading list JSON file not found.</p>";
            return false;
        }
        
        $readingListData = json_decode(file_get_contents($jsonFile), true);
        
        if (!isset($readingListData['books']) || empty($readingListData['books'])) {
            echo "<p class='error'>No reading list items found in JSON file.</p>";
            return false;
        }
        
        // Prepare insert statement
        $stmt = $conn->prepare("
            INSERT INTO reading_list (book_id, total_pages, current_page, 
                                     completed, date_added)
            VALUES (:book_id, :total_pages, :current_page, 
                   :completed, :date_added)
        ");
        
        // Insert each reading list item
        $count = 0;
        foreach ($readingListData['books'] as $item) {
            $params = [
                ':book_id' => $item['id'],
                ':total_pages' => $item['totalPages'] ?? 1,
                ':current_page' => $item['currentPage'] ?? 0,
                ':completed' => $item['completed'] ?? false,
                ':date_added' => $item['dateAdded'] ?? date('Y-m-d H:i:s')
            ];
            
            $stmt->execute($params);
            $count++;
        }
        
        echo "<p class='success'>Migrated $count reading list items successfully.</p>";
        return true;
    } catch (PDOException $e) {
        echo "<p class='error'>Error migrating reading list: " . $e->getMessage() . "</p>";
        return false;
    }
}

// Function to migrate reviews from JSON to MySQL
function migrateReviews($conn) {
    echo "<h2>Migrating Reviews</h2>";
    
    try {
        // Check if the reviews table already has data
        $stmt = $conn->query("SELECT COUNT(*) FROM reviews");
        $count = $stmt->fetchColumn();
        
        if ($count > 0) {
            echo "<p>Reviews table already has data. Skipping migration.</p>";
            return true;
        }
        
        // Read JSON file
        $jsonFile = 'src/data/reviews.json';
        if (!file_exists($jsonFile)) {
            echo "<p class='error'>Reviews JSON file not found.</p>";
            return false;
        }
        
        $reviewsData = json_decode(file_get_contents($jsonFile), true);
        
        if (!isset($reviewsData['reviews']) || empty($reviewsData['reviews'])) {
            echo "<p>No reviews found in JSON file. Nothing to migrate.</p>";
            return true;
        }
        
        // Prepare insert statement
        $stmt = $conn->prepare("
            INSERT INTO reviews (book_id, reviewer_name, rating, 
                               comment, review_date)
            VALUES (:book_id, :reviewer_name, :rating, 
                   :comment, :review_date)
        ");
        
        // Insert each review
        $count = 0;
        foreach ($reviewsData['reviews'] as $review) {
            $params = [
                ':book_id' => $review['bookId'],
                ':reviewer_name' => $review['reviewerName'],
                ':rating' => $review['rating'],
                ':comment' => $review['comment'] ?? '',
                ':review_date' => $review['reviewDate'] ?? date('Y-m-d H:i:s')
            ];
            
            $stmt->execute($params);
            $count++;
        }
        
        echo "<p class='success'>Migrated $count reviews successfully.</p>";
        return true;
    } catch (PDOException $e) {
        echo "<p class='error'>Error migrating reviews: " . $e->getMessage() . "</p>";
        return false;
    }
}

// Run the migration
echo "<h2>Starting Migration</h2>";
echo "<pre>Server: $host\nDatabase: $dbname\nUsername: $username</pre>";

// Create database schema
$schemaCreated = createDatabaseSchema($conn);
if (!$schemaCreated) {
    die("<p class='error'>Failed to create database schema. Migration aborted.</p>");
}

// Migrate books
$booksMigrated = migrateBooks($conn);
if (!$booksMigrated) {
    echo "<p class='error'>Failed to migrate books.</p>";
}

// Migrate reading list
$readingListMigrated = migrateReadingList($conn);
if (!$readingListMigrated) {
    echo "<p class='error'>Failed to migrate reading list.</p>";
}

// Migrate reviews
$reviewsMigrated = migrateReviews($conn);
if (!$reviewsMigrated) {
    echo "<p class='error'>Failed to migrate reviews.</p>";
}

// Migration summary
echo "<h2>Migration Complete</h2>";
echo "<p>Database migration has been completed.</p>";
echo "<p>You can now access the application at <a href='index.html'>Vintage Library</a>.</p>";
?> 