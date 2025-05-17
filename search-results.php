<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "mhmd090"; // If you have a password, replace this
$dbname = "vintage_library";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get search parameters
$search_query = isset($_GET['q']) ? $_GET['q'] : '';
$genre_filter = isset($_GET['genre']) ? $_GET['genre'] : '';
$sort_by = isset($_GET['sort']) ? $_GET['sort'] : 'relevance';

// Define available filters
$available_genres = [];
$genre_query = "SELECT DISTINCT genre FROM books ORDER BY genre";
$genre_result = $conn->query($genre_query);
if ($genre_result && $genre_result->num_rows > 0) {
    while ($row = $genre_result->fetch_assoc()) {
        $available_genres[] = $row['genre'];
    }
}

$available_languages = [];
$language_query = "SELECT DISTINCT language FROM books ORDER BY language";
$language_result = $conn->query($language_query);
if ($language_result && $language_result->num_rows > 0) {
    while ($row = $language_result->fetch_assoc()) {
        $available_languages[] = $row['language'];
    }
}

$available_conditions = [];
$condition_query = "SELECT DISTINCT `condition` FROM books ORDER BY `condition`";
$condition_result = $conn->query($condition_query);
if ($condition_result && $condition_result->num_rows > 0) {
    while ($row = $condition_result->fetch_assoc()) {
        $available_conditions[] = $row['condition'];
    }
}

$available_formats = [];
$format_query = "SELECT DISTINCT format FROM books ORDER BY format";
$format_result = $conn->query($format_query);
if ($format_result && $format_result->num_rows > 0) {
    while ($row = $format_result->fetch_assoc()) {
        $available_formats[] = $row['format'];
    }
}

// Get favorites for marking books
$favorite_book_ids = [];
$favorites_query = "SELECT book_id FROM favorites";
$favorites_result = $conn->query($favorites_query);
if ($favorites_result && $favorites_result->num_rows > 0) {
    while ($row = $favorites_result->fetch_assoc()) {
        $favorite_book_ids[] = $row['book_id'];
    }
}

// Count total favorites
$total_favorites_count = count($favorite_book_ids);

// Process search and filters
$sql = "SELECT * FROM books WHERE 1=1";
$params = [];
$types = "";

if (!empty($search_query)) {
    $sql .= " AND (title LIKE ? OR author LIKE ? OR genre LIKE ? OR description LIKE ?)";
    $search_param = "%$search_query%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= "ssss";
}

if (!empty($genre_filter)) {
    $sql .= " AND genre = ?";
    $params[] = $genre_filter;
    $types .= "s";
}

// Apply sorting
switch ($sort_by) {
    case 'title':
        $sql .= " ORDER BY title ASC";
        break;
    case 'author':
        $sql .= " ORDER BY author ASC";
        break;
    case 'date':
        $sql .= " ORDER BY publish_date DESC";
        break;
    case 'rating':
        // If you have a rating field or join with reviews table
        // $sql .= " ORDER BY rating DESC";
        $sql .= " ORDER BY id DESC"; // Default for now
        break;
    default:
        // Default or "relevance" - could use custom ranking logic here
        if (!empty($search_query)) {
            // For relevance, prioritize title matches
            $sql .= " ORDER BY 
                      CASE WHEN title LIKE ? THEN 1
                           WHEN author LIKE ? THEN 2
                           WHEN genre LIKE ? THEN 3
                           ELSE 4 END,
                      title ASC";
            $search_param = "%$search_query%";
            $params[] = $search_param;
            $params[] = $search_param;
            $params[] = $search_param;
            $types .= "sss";
        } else {
            $sql .= " ORDER BY title ASC";
        }
        break;
}

// Prepare and execute
$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$books = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        // Mark if this book is a favorite
        $row['is_favorite'] = in_array($row['id'], $favorite_book_ids);
        $books[] = $row;
    }
}
$results_count = count($books);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results - Vintage Library</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Libre+Baskerville:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="src/styles/main.css">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        /* Custom tooltip styling */
        .tooltip {
            font-family: 'Libre Baskerville', serif;
        }
        
        .tooltip .tooltip-inner {
            background-color: #8b7355;
            color: white;
            padding: 8px 16px;
            border-radius: 4px;
            font-size: 0.875rem;
        }

        .tooltip.bs-tooltip-top .tooltip-arrow::before {
            border-top-color: #8b7355;
        }

        .tooltip.bs-tooltip-bottom .tooltip-arrow::before {
            border-bottom-color: #8b7355;
        }

        /* Replace the existing card styling with this minimalistic version */
        .book-card {
            height: 100%;
            min-height: 400px;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            margin: 0;
        }

        .book-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }

        .book-image-container {
            position: relative;
            width: 100%;
            padding-top: 140%; /* This creates a 1.4:1 aspect ratio */
            overflow: hidden;
        }

        .book-image {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .book-card:hover .book-image {
            transform: scale(1.05);
        }

        .book-info {
            padding: 1.25rem;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .book-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.1rem;
            line-height: 1.4;
            color: #2d2d2d;
            margin: 0;
        }

        .book-author {
            font-size: 0.9rem;
            color: #666;
            margin: 0;
            line-height: 1.4;
        }

        .book-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.8rem;
            color: #8b7355;
            margin-top: auto;
            padding-top: 0.75rem;
            border-top: 1px solid #eee;
        }

        .book-meta span {
            display: flex;
            align-items: center;
            gap: 0.375rem;
        }

        .book-meta i {
            width: 16px;
            height: 16px;
        }

        /* Enhanced search bar styling */
        .search-container {
            background: linear-gradient(rgba(139, 115, 85, 0.1), rgba(139, 115, 85, 0.05));
            padding: 2rem;
            border-radius: 20px;
            margin-bottom: 3rem;
        }

        .search-input-group {
            background: white;
            border-radius: 50px;
            padding: 0.5rem;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .search-input {
            border: none;
            padding: 0.8rem 1.5rem;
            font-size: 1.1rem;
            flex: 1 1 250px;
            min-width: 0;
            background: transparent;
        }

        .search-input:focus {
            outline: none;
        }

        /* Mobile responsive search styles */
        @media (max-width: 768px) {
            .search-container {
                padding: 1rem;
                margin-bottom: 2rem;
                border-radius: 15px;
            }

            .search-input-group {
                padding: 0.5rem;
                flex-direction: column;
                border-radius: 15px;
                gap: 0.75rem;
            }

            .search-input {
                width: 100%;
                padding: 0.6rem 1rem;
                font-size: 1rem;
                order: 2;
            }

            .search-input-group i {
                order: 1;
                margin: 0 !important;
                align-self: flex-start;
                padding-left: 1rem;
            }

            #sortSelect {
                width: 100% !important;
                order: 3;
                border-top: 1px solid #eee !important;
                padding-top: 0.75rem;
                margin-top: 0.25rem;
            }

            .search-input-group .btn {
                width: 100%;
                order: 4;
                margin: 0 !important;
            }
        }

        /* Tablet responsive search styles */
        @media (min-width: 769px) and (max-width: 991px) {
            .search-container {
                padding: 1.5rem;
            }

            .search-input-group {
                flex-wrap: wrap;
            }

            .search-input {
                flex: 1 1 200px;
            }

            #sortSelect {
                flex: 1 1 150px;
            }

            .search-input-group .btn {
                flex: 0 0 auto;
            }
        }

        /* Updated filter styling */
        .filter-section {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1rem;
        }

        .filter-group {
            border-bottom: 1px solid #eee;
            padding-bottom: 1rem;
            margin-bottom: 1rem;
        }

        .filter-group:last-child {
            border-bottom: none;
            padding-bottom: 0;
            margin-bottom: 0;
        }

        .filter-label {
            font-family: 'Playfair Display', serif;
            color: #2d2d2d;
            margin-bottom: 1rem;
        }

        /* Custom checkbox styling */
        .custom-checkbox {
            display: flex;
            align-items: center;
            padding: 0.5rem 0;
            cursor: pointer;
        }

        .custom-checkbox input {
            display: none;
        }

        .checkbox-indicator {
            width: 18px;
            height: 18px;
            border: 2px solid #8b7355;
            border-radius: 4px;
            margin-right: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }

        .custom-checkbox input:checked + .checkbox-indicator {
            background-color: #8b7355;
        }

        .checkbox-indicator:after {
            content: '✓';
            color: white;
            display: none;
        }

        .custom-checkbox input:checked + .checkbox-indicator:after {
            display: block;
        }

        .vintage-btn {
            background-color: #8b7355;
            color: white;
            border: none;
            transition: all 0.3s ease;
        }

        .vintage-btn:hover {
            background-color: #6d5a43;
            color: white;
            box-shadow: 0 4px 12px rgba(139, 115, 85, 0.2);
        }

        .vintage-outline-btn {
            background-color: transparent;
            color: #8b7355;
            border: 2px solid #8b7355;
        }

        .vintage-outline-btn:hover {
            background-color: #8b7355;
            color: white;
            box-shadow: 0 4px 12px rgba(139, 115, 85, 0.2);
        }

        .vintage-outline-btn.favorited {
            background-color: rgba(217, 179, 16, 0.1);
            color: #D9B310;
            border: 2px solid #D9B310;
        }

        .vintage-outline-btn.favorited:hover {
            background-color: #D9B310;
            color: white;
            box-shadow: 0 4px 12px rgba(217, 179, 16, 0.2);
        }

        .fade-in {
            opacity: 0;
            animation: fadeIn 0.5s ease-in forwards;
            animation-delay: calc(var(--animation-order) * 0.1s);
        }

        @keyframes fadeIn {
            from { 
                opacity: 0; 
                transform: translateY(20px); 
            }
            to { 
                opacity: 1; 
                transform: translateY(0); 
            }
        }

        .results-count {
            color: #8b7355;
            font-family: 'Playfair Display', serif;
        }

        .no-results {
            text-align: center;
            padding: 4rem 2rem;
        }

        .no-results i {
            color: #8b7355;
            margin-bottom: 1rem;
        }

        .book-actions {
            display: flex;
            gap: 0.75rem;
            margin-top: 1rem;
            padding-top: 0.75rem;
            border-top: 1px solid #eee;
        }

        .book-actions button {
            flex: 1;
            padding: 0.625rem;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        /* Update the search results container */
        #searchResults {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1.5rem;
            padding: 1rem 0;
        }

        /* Add a media query for smaller screens */
        @media (max-width: 576px) {
            #searchResults {
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
                gap: 1rem;
            }

            .book-card {
                min-height: 350px;
            }
        }

        /* Favorites Panel Styles */
        .favorites-panel {
            position: absolute;
            top: 100%;
            right: 0;
            width: 320px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
            opacity: 0;
            visibility: hidden;
            transform: translateY(10px);
            transition: all 0.3s ease;
            z-index: 1000;
            max-height: 480px;
            overflow-y: auto;
        }

        .favorites-container {
            position: relative;
        }

        .favorites-container:hover .favorites-panel {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .favorites-header {
            padding: 1rem;
            border-bottom: 1px solid rgba(139, 115, 85, 0.1);
            background: rgba(139, 115, 85, 0.03);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 1;
        }

        .favorites-header h6 {
            color: #2d2d2d;
            margin: 0;
        }

        .favorite-item {
            display: flex;
            align-items: center;
            padding: 1rem;
            border-bottom: 1px solid rgba(139, 115, 85, 0.1);
            transition: background-color 0.2s ease;
        }

        .favorite-item:hover {
            background-color: rgba(139, 115, 85, 0.05);
        }

        .favorite-item:last-child {
            border-bottom: none;
        }

        .favorite-item img {
            width: 48px;
            height: 72px;
            object-fit: cover;
            border-radius: 6px;
            margin-right: 1rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .favorite-item-info {
            flex-grow: 1;
            min-width: 0;
        }

        .favorite-item-title {
            font-family: 'Playfair Display', serif;
            font-size: 0.95rem;
            margin: 0 0 0.25rem 0;
            color: #2d2d2d;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .favorite-item-author {
            font-size: 0.85rem;
            color: #666;
            margin: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .favorite-item-remove {
            background: none;
            border: none;
            padding: 0.5rem;
            color: #8b7355;
            cursor: pointer;
            opacity: 0.7;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .favorite-item-remove:hover {
            opacity: 1;
            transform: scale(1.1);
        }

        .favorite-item-remove i {
            width: 18px;
            height: 18px;
        }

        .favorites-empty {
            padding: 2.5rem 1.5rem;
            text-align: center;
            color: #666;
        }

        .favorites-empty i {
            color: #8b7355;
            opacity: 0.5;
            margin-bottom: 1rem;
            width: 32px;
            height: 32px;
        }

        .favorites-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: #8b7355;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            box-shadow: 0 2px 4px rgba(139, 115, 85, 0.3);
        }

        /* Add scrollbar styling */
        .favorites-panel::-webkit-scrollbar {
            width: 8px;
        }

        .favorites-panel::-webkit-scrollbar-track {
            background: rgba(139, 115, 85, 0.05);
            border-radius: 0 12px 12px 0;
        }

        .favorites-panel::-webkit-scrollbar-thumb {
            background: rgba(139, 115, 85, 0.2);
            border-radius: 4px;
        }

        .favorites-panel::-webkit-scrollbar-thumb:hover {
            background: rgba(139, 115, 85, 0.3);
        }

        /* Toast Styles */
        .toast-container {
            position: fixed;
            bottom: 1rem;
            right: 1rem;
            z-index: 1050;
        }

        .toast {
            background: white;
            border-radius: 4px;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            margin-bottom: 0.75rem;
        }

        .toast-body {
            padding: 0.75rem 1.25rem;
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark custom-nav">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <i data-lucide="library" class="me-2"></i>
                <span class="font-playfair">Vintage Library</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-label="Toggle navigation" title="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link px-2" href="index.php" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Home">
                            <i data-lucide="home"></i>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-2 active" href="search-results.php" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Browse Books">
                            <i data-lucide="book-open"></i>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-2" href="categories.php" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Categories">
                            <i data-lucide="list"></i>
                        </a>
                    </li>
                    <li class="nav-item favorites-container">
                        <a class="nav-link px-2 position-relative" href="#" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Favorites" data-bs-auto-close="outside">
                            <i data-lucide="heart"></i>
                            <span class="favorites-badge" id="favoritesCount"><?php echo $total_favorites_count; ?></span>
                        </a>
                        <div class="favorites-panel">
                            <div class="favorites-header">
                                <h6 class="mb-0 font-playfair">Favorite Books</h6>
                                <small class="text-muted">Recently Added</small>
                            </div>
                            <div id="favoritesList">
                                <?php 
                                // Fetch favorites for the dropdown
                                $sql_favorites = "SELECT b.id, b.title, b.author, b.image_url FROM favorites f JOIN books b ON f.book_id = b.id ORDER BY f.date_added DESC LIMIT 5";
                                $result_favorites = $conn->query($sql_favorites);
                                $favorites = [];
                                if ($result_favorites && $result_favorites->num_rows > 0) {
                                    while($row = $result_favorites->fetch_assoc()) {
                                        $favorites[] = $row;
                                    }
                                }
                                
                                if (empty($favorites)): 
                                ?>
                                    <div class="favorites-empty">
                                        <i data-lucide="heart-off" class="mb-2"></i>
                                        <p class="mb-0">No favorite books yet</p>
                                    </div>
                                <?php else: ?>
                                    <?php foreach ($favorites as $book): ?>
                                        <div class="favorite-item">
                                            <img src="<?php echo htmlspecialchars($book['image_url']); ?>" alt="<?php echo htmlspecialchars($book['title']); ?>">
                                            <div class="favorite-item-info">
                                                <h6 class="favorite-item-title"><?php echo htmlspecialchars($book['title']); ?></h6>
                                                <p class="favorite-item-author"><?php echo htmlspecialchars($book['author']); ?></p>
                                            </div>
                                            <a href="fix-favorites.php?action=remove&book_id=<?php echo $book['id']; ?>&return_url=search-results.php<?php echo !empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : ''; ?>" class="favorite-item-remove" title="Remove Favorite">
                                                <i data-lucide="x"></i>
                                            </a>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-2" href="reading-list.php" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Reading List">
                            <i data-lucide="list-checks"></i>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-2" href="events.php" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Events">
                            <i data-lucide="calendar"></i>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-2" href="gallery.php" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Gallery">
                            <i data-lucide="image"></i>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-2" href="reviews.php" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Reviews">
                            <i data-lucide="star"></i>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-2" href="add-book.php" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Add Book">
                            <i data-lucide="plus-circle"></i>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-2" href="about.php" data-bs-toggle="tooltip" data-bs-placement="bottom" title="About">
                            <i data-lucide="info"></i>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-2" href="contact.php" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Contact">
                            <i data-lucide="mail"></i>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container py-4 flex-grow-1">
        <div class="search-container">
            <form id="searchForm" method="GET" action="search-results.php" class="mb-4">
                <div class="search-input-group">
                    <i data-lucide="search" class="ms-3" style="color: #8b7355;"></i>
                    <input 
                        type="text" 
                        class="search-input" 
                        id="searchInput" 
                        name="q"
                        placeholder="Search by title, author, genre, or keyword..."
                        value="<?php echo htmlspecialchars($search_query); ?>"
                    >
                    <select class="form-select border-0" id="sortSelect" name="sort" style="width: auto;" title="Sort results by" aria-label="Sort by">
                        <option value="relevance" <?php echo $sort_by == 'relevance' ? 'selected' : ''; ?>>Most Relevant</option>
                        <option value="title" <?php echo $sort_by == 'title' ? 'selected' : ''; ?>>Title (A-Z)</option>
                        <option value="author" <?php echo $sort_by == 'author' ? 'selected' : ''; ?>>Author (A-Z)</option>
                        <option value="date" <?php echo $sort_by == 'date' ? 'selected' : ''; ?>>Publication Date</option>
                        <option value="rating" <?php echo $sort_by == 'rating' ? 'selected' : ''; ?>>Highest Rated</option>
                    </select>
                    <button type="submit" class="btn vintage-btn rounded-pill px-4 me-2">
                        Search
                    </button>
                </div>
            </form>
            <?php if (!empty($search_query) || !empty($genre_filter)): ?>
            <div class="active-filters" id="activeFilters">
                <?php if (!empty($search_query)): ?>
                <span class="badge bg-light text-dark me-2">
                    Search: <?php echo htmlspecialchars($search_query); ?>
                    <a href="search-results.php<?php echo !empty($genre_filter) ? '?genre=' . urlencode($genre_filter) : ''; ?>" class="ms-2 text-dark" title="Remove filter">×</a>
                </span>
                <?php endif; ?>
                
                <?php if (!empty($genre_filter)): ?>
                <span class="badge bg-light text-dark me-2">
                    Genre: <?php echo htmlspecialchars($genre_filter); ?>
                    <a href="search-results.php<?php echo !empty($search_query) ? '?q=' . urlencode($search_query) : ''; ?>" class="ms-2 text-dark" title="Remove filter">×</a>
                </span>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>

        <div class="row g-4">
            <!-- Filters Sidebar -->
            <div class="col-lg-3">
                <div class="filter-section">
                    <h3 class="filter-heading">Refine Results</h3>
                    
                    <div class="filter-group">
                        <div class="filter-label">Genre</div>
                        <?php foreach ($available_genres as $genre): ?>
                        <label class="custom-checkbox">
                            <input type="checkbox" name="genre" value="<?php echo htmlspecialchars($genre); ?>" 
                                <?php echo ($genre_filter == $genre) ? 'checked' : ''; ?>
                                onclick="window.location.href='search-results.php?<?php 
                                    $params = $_GET;
                                    if ($genre_filter == $genre) {
                                        unset($params['genre']);
                                    } else {
                                        $params['genre'] = $genre;
                                    }
                                    echo http_build_query($params);
                                ?>'">
                            <span class="checkbox-indicator"></span>
                            <?php echo htmlspecialchars($genre); ?>
                        </label>
                        <?php endforeach; ?>
                    </div>

                    <div class="filter-group">
                        <div class="filter-label">Language</div>
                        <?php foreach ($available_languages as $language): ?>
                        <label class="custom-checkbox">
                            <input type="checkbox" name="language" value="<?php echo htmlspecialchars($language); ?>">
                            <span class="checkbox-indicator"></span>
                            <?php echo htmlspecialchars($language); ?>
                        </label>
                        <?php endforeach; ?>
                    </div>

                    <div class="filter-group">
                        <div class="filter-label">Condition</div>
                        <?php foreach ($available_conditions as $condition): ?>
                        <label class="custom-checkbox">
                            <input type="checkbox" name="condition" value="<?php echo htmlspecialchars($condition); ?>">
                            <span class="checkbox-indicator"></span>
                            <?php echo htmlspecialchars($condition); ?>
                        </label>
                        <?php endforeach; ?>
                    </div>

                    <div class="filter-group">
                        <div class="filter-label">Format</div>
                        <?php foreach ($available_formats as $format): ?>
                        <label class="custom-checkbox">
                            <input type="checkbox" name="format" value="<?php echo htmlspecialchars($format); ?>">
                            <span class="checkbox-indicator"></span>
                            <?php echo htmlspecialchars($format); ?>
                        </label>
                        <?php endforeach; ?>
                    </div>

                    <button class="btn vintage-btn w-100 mt-3" onclick="window.location.href='search-results.php'">
                        Clear All Filters
                    </button>
                </div>
            </div>

            <!-- Results Section -->
            <div class="col-lg-9">
                <h4 class="results-count mb-4">Showing <span id="resultsCount"><?php echo $results_count; ?></span> results</h4>
                <div id="searchResults">
                    <?php if ($results_count === 0): ?>
                    <div class="col-12">
                        <div class="no-results">
                            <i data-lucide="book-x" style="width: 48px; height: 48px;"></i>
                            <h3 class="font-playfair">No Results Found</h3>
                            <p class="text-muted">Try adjusting your search or filters</p>
                        </div>
                    </div>
                    <?php else: ?>
                        <?php foreach ($books as $index => $book): ?>
                        <div class="fade-in" style="--animation-order: <?php echo $index; ?>">
                            <div class="book-card">
                                <div class="book-image-container">
                                    <img src="<?php echo htmlspecialchars($book['image_url']); ?>" alt="<?php echo htmlspecialchars($book['title']); ?>" class="book-image">
                                </div>
                                <div class="book-info">
                                    <h3 class="book-title"><?php echo htmlspecialchars($book['title']); ?></h3>
                                    <p class="book-author">by <?php echo htmlspecialchars($book['author']); ?></p>
                                    <div class="book-meta">
                                        <span><i data-lucide="bookmark"></i> <?php echo htmlspecialchars($book['genre']); ?></span>
                                        <span><i data-lucide="book-copy"></i> <?php echo (int)$book['copies_available']; ?> available</span>
                                    </div>
                                    <div class="book-actions">
                                        <a href="reading-list.php?action=add&book_id=<?php echo $book['id']; ?>&return_url=search-results.php<?php echo !empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : ''; ?>" class="btn vintage-btn">
                                            <i data-lucide="book-marked" class="me-1"></i>
                                            Borrow
                                        </a>
                                        <a href="fix-favorites.php?action=<?php echo $book['is_favorite'] ? 'remove' : 'add'; ?>&book_id=<?php echo $book['id']; ?>&return_url=search-results.php<?php echo !empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : ''; ?>" 
                                          class="btn vintage-outline-btn <?php echo $book['is_favorite'] ? 'favorited' : ''; ?>"
                                          title="<?php echo $book['is_favorite'] ? 'Remove from favorites' : 'Add to favorites'; ?>">
                                            <i data-lucide="<?php echo $book['is_favorite'] ? 'heart' : 'heart-off'; ?>" class="me-1"></i>
                                            <?php echo $book['is_favorite'] ? 'Favorited' : 'Favorite'; ?>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <!-- Add the modal for adding to reading list -->
    <div class="modal fade" id="addBookModal" tabindex="-1" aria-labelledby="addBookModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                    <h5 class="modal-title font-playfair" id="addBookModalLabel">Add to Reading List</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                    <input type="hidden" id="bookId">
                                <div class="mb-3">
                        <label for="modalTotalPages" class="form-label">How many pages does this book have?</label>
                        <input type="number" class="form-control" id="modalTotalPages" value="100" min="1" title="Total pages">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn vintage-btn" onclick="confirmAddToReadingList()">Add to Reading List</button>
                            </div>
                        </div>
                    </div>
                </div>

    <div id="toastContainer" class="toast-container position-fixed bottom-0 end-0 p-3"></div>

    <!-- Same Footer as index.html -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Initialize tooltips
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

            // Initialize Lucide icons
            lucide.createIcons();
            
        // We no longer need the old JavaScript functions since PHP handles data loading and rendering
        // Just keeping the minimum JS needed for interactive UI elements
    </script>
    <?php
    // Close the database connection
    if (isset($conn)) {
        $conn->close();
    }
    ?>
</body>
</html>
</html>