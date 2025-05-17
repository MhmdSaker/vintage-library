<?php
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

// Fetch favorites
$favorites = [];
$sql_favorites = "SELECT b.id, b.title, b.author, b.image_url FROM favorites f JOIN books b ON f.book_id = b.id ORDER BY f.date_added DESC LIMIT 5";
// Assuming you want to show the latest 5. Adjust as needed.
// Also, ensure user context for favorites if you implement user logins later (e.g., WHERE f.user_id = X)
$result_favorites = $conn->query($sql_favorites);
if ($result_favorites && $result_favorites->num_rows > 0) {
    while ($row = $result_favorites->fetch_assoc()) {
        $favorites[] = $row;
    }
}

// Fetch total favorite count
$total_favorites_count = 0;
$sql_total_favorites = "SELECT COUNT(*) as count FROM favorites"; // Add user context if needed
$result_total_favorites = $conn->query($sql_total_favorites);
if ($result_total_favorites) {
    $total_favorites_count = $result_total_favorites->fetch_assoc()['count'];
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vintage Library</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Libre+Baskerville:wght@400;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="src/styles/main.css">
    <!-- Update Lucide script to latest version -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
    <style>
        .book-card {
            height: 100%;
            perspective: 1000px;
            border: none;
            
        }

        .book-card-inner {
            position: relative;
            width: 100%;
            height: 100%;
            text-align: center;
            transition: transform 0.8s;
            transform-style: preserve-3d;
            cursor: pointer;
        }

        .book-card:hover .book-card-inner {
            transform: rotateY(180deg);
        }

        .book-card-front, .book-card-back {
            position: absolute;
            width: 100%;
            height: 100%;
            backface-visibility: hidden;
            border-radius: 12px;
            overflow: hidden;
        }

        .book-card-front {
            background: #fff;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .book-card-back {
            background: #fff;
            transform: rotateY(180deg);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
        }

        .book-card img {
            width: 100%;
            height: 400px;
            object-fit: cover;
            border-radius: 12px;
        }

        .book-card-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);
            padding: 2rem 1rem 1rem;
            border-radius: 0 0 12px 12px;
        }

        .book-title {
            color: #fff;
            font-size: 1.25rem;
            margin-bottom: 0.5rem;
            text-shadow: 1px 1px 3px rgba(0,0,0,0.3);
        }

        .book-author {
            color: rgba(255,255,255,0.9);
            font-size: 0.9rem;
        }

        .book-description {
            flex-grow: 1;
            margin-bottom: 1.5rem;
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 5;
            line-clamp: 5;
            -webkit-box-orient: vertical;
        }

        .btn-group {
            gap: 0.5rem;
        }

        .vintage-btn {
            background-color: #8b7355;
            color: white;
            transition: all 0.3s ease;
        }

        .vintage-btn:hover {
            background-color: #6d5a43;
            transform: translateY(-2px);
            color: white;
        }

        .vintage-outline-btn {
            border: 1px solid #8b7355;
            color: #8b7355;
            transition: all 0.3s ease;
        }

        .vintage-outline-btn:hover {
            background-color: #8b7355;
            color: white;
            transform: translateY(-2px);
        }

        .vintage-link {
            color: #8b7355;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .vintage-link:hover {
            color: #6d5a43;
            transform: translateY(-2px);
        }

        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .search-box-large {
            background: white;
            border-radius: 50px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 8px;
            transition: all 0.3s ease;
        }

        .search-box-large:focus-within {
            box-shadow: 0 15px 40px rgba(139, 115, 85, 0.2);
            transform: translateY(-2px);
        }

        .search-input-large {
            border: none;
            border-radius: 50px;
            padding-left: 25px;
            padding-right: 150px;
            font-size: 1.1rem;
        }

        .search-input-large:focus {
            box-shadow: none;
        }

        .search-btn-large {
            position: absolute;
            right: 8px;
            top: 8px;
            border-radius: 50px;
            padding-left: 25px;
            padding-right: 25px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .badge {
            font-size: 0.9rem;
            padding: 8px 16px;
            text-decoration: none;
            transition: all 0.3s ease;
            border: 1px solid #dee2e6;
        }

        .badge:hover {
            background-color: #8b7355 !important;
            color: white !important;
            transform: translateY(-2px);
        }

        .favorites-panel {
            position: absolute;
            top: 100%;
            right: 0;
            width: 300px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            opacity: 0;
            visibility: hidden;
            transform: translateY(10px);
            transition: all 0.3s ease;
            z-index: 1000;
            max-height: 400px;
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

        .favorite-item {
            display: flex;
            align-items: center;
            padding: 0.75rem;
            border-bottom: 1px solid #eee;
            transition: background-color 0.2s ease;
        }

        .favorite-item:hover {
            background-color: rgba(139, 115, 85, 0.05);
        }

        .favorite-item:last-child {
            border-bottom: none;
        }

        .favorite-item img {
            width: 40px;
            height: 60px;
            object-fit: cover;
            border-radius: 4px;
            margin-right: 0.75rem;
        }

        .favorite-item-info {
            flex-grow: 1;
        }

        .favorite-item-title {
            font-family: 'Playfair Display', serif;
            font-size: 0.9rem;
            margin: 0;
            color: #2d2d2d;
        }

        .favorite-item-author {
            font-size: 0.8rem;
            color: #666;
            margin: 0;
        }

        .favorite-item-remove {
            background: none;
            border: none;
            padding: 0.25rem;
            color: #8b7355;
            cursor: pointer;
            opacity: 0.7;
            transition: opacity 0.2s ease;
        }

        .favorite-item-remove:hover {
            opacity: 1;
        }

        .favorites-header {
            padding: 1rem;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .favorites-empty {
            padding: 2rem 1rem;
            text-align: center;
            color: #666;
        }

        .favorites-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: #8b7355;
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 0.7rem;
            display: flex;
            align-items: center;
            justify-content: center;
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
                        <a class="nav-link px-2 active" href="index.php" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Home">
                            <i data-lucide="home"></i>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-2" href="search-results.php" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Browse Books">
                            <i data-lucide="book-open"></i>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-2" href="categories.php" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Categories">
                            <i data-lucide="list"></i>
                        </a>
                    </li>
                    <li class="nav-item favorites-container">
                        <a class="nav-link px-2 position-relative" href="#" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Favorites">
                            <i data-lucide="heart"></i>
                            <span class="favorites-badge" id="favoritesCount"><?php echo $total_favorites_count; ?></span>
                        </a>
                        <div class="favorites-panel">
                            <div class="favorites-header">
                                <h6 class="mb-0 font-playfair">Favorite Books</h6>
                                <small class="text-muted">Recently Added</small>
                            </div>
                            <div id="favoritesList">
                                <?php if (empty($favorites)): ?>
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
                                            <!-- Basic remove link, assumes a GET request to a handler.
                                                 For robust POST, a small form per item or JS to POST would be needed.
                                                 This example redirects to fix-favorites.php which you might need to adapt/create. -->
                                            <a href="fix-favorites.php?action=remove&book_id=<?php echo $book['id']; ?>&return_url=index.php" class="favorite-item-remove" title="Remove Favorite">
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




    
    <!-- Main Content -->
    <main class="container-fluid py-4 flex-grow-1 d-flex align-items-center justify-content-center" style="min-height: calc(100vh - 150px);">
        <div class="text-center" style="max-width: 800px;">
            <h1 class="display-4 font-playfair mb-4">Discover Classic Literature</h1>
            <p class="lead text-muted mb-5">Explore our carefully curated collection of timeless masterpieces</p>
            
            <form id="searchForm" action="search-results.php" class="mx-auto" style="max-width: 600px;">
                <div class="search-box-large position-relative">
                    <input 
                        type="text" 
                        class="form-control form-control-lg search-input-large" 
                        id="searchInput" 
                        name="q"
                        placeholder="Search by title, author, or genre..."
                        autocomplete="off"
                    >
                    <button type="submit" class="btn btn-lg vintage-btn search-btn-large">
                        <i data-lucide="search"></i>
                        Search
                    </button>
                </div>
                <div class="mt-4 text-muted">
                    <p class="mb-2">Popular searches:</p>
                    <div class="d-flex gap-2 justify-content-center flex-wrap">
                        <a href="search-results.php?genre=Classic+Romance" class="badge rounded-pill text-bg-light">Classic Romance</a>
                        <a href="search-results.php?genre=Gothic+Fiction" class="badge rounded-pill text-bg-light">Gothic Fiction</a>
                        <a href="search-results.php?genre=Mystery" class="badge rounded-pill text-bg-light">Mystery</a>
                        <a href="search-results.php?genre=Poetry" class="badge rounded-pill text-bg-light">Poetry</a>
                    </div>
                </div>
            </form>
        </div>
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS for UI interactions like tooltips and Lucide icons, if still needed -->
    <!-- <script src="src/js/main.js"></script> --> <!-- Assuming main.js might have general UI enhancements not related to data -->

    <!-- Updated Footer -->
    <footer class="footer mt-auto">
        <div class="footer-top py-5">
            <div class="container">
                <div class="row g-4">
                    <div class="col-lg-4 col-md-6">
                        <div class="footer-info">
                            <div class="d-flex align-items-center mb-4">
                                <i data-lucide="library" class="me-2 footer-logo"></i>
                                <h3 class="font-playfair mb-0">Vintage Library</h3>
                            </div>
                            <p class="mb-4">Preserving and sharing timeless literary masterpieces with readers around the world.</p>
                            <div class="social-links">
                                <a href="#" class="social-link" data-bs-toggle="tooltip" data-bs-placement="top" title="Facebook">
                                    <i data-lucide="facebook"></i>
                                </a>
                                <a href="#" class="social-link" data-bs-toggle="tooltip" data-bs-placement="top" title="Twitter">
                                    <i data-lucide="twitter"></i>
                                </a>
                                <a href="#" class="social-link" data-bs-toggle="tooltip" data-bs-placement="top" title="Instagram">
                                    <i data-lucide="instagram"></i>
                                </a>
                                <a href="#" class="social-link" data-bs-toggle="tooltip" data-bs-placement="top" title="YouTube">
                                    <i data-lucide="youtube"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <h4 class="font-playfair mb-4">Quick Links</h4>
                        <ul class="footer-links">
                            <li>
                                <i data-lucide="chevron-right"></i>
                                <a href="index.php">Browse Books</a>
                            </li>
                            <li>
                                <i data-lucide="chevron-right"></i>
                                <a href="categories.php">Categories</a>
                            </li>
                            <li>
                                <i data-lucide="chevron-right"></i>
                                <a href="events.php">Events</a>
                            </li>
                            <li>
                                <i data-lucide="chevron-right"></i>
                                <a href="about.php">About Us</a>
                            </li>
                        </ul>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <h4 class="font-playfair mb-4">Library Hours</h4>
                        <ul class="footer-hours">
                            <li>
                                <i data-lucide="clock"></i>
                                <span>Monday - Friday</span>
                                <span>9:00 AM - 8:00 PM</span>
                            </li>
                            <li>
                                <i data-lucide="clock"></i>
                                <span>Saturday</span>
                                <span>10:00 AM - 6:00 PM</span>
                            </li>
                            <li>
                                <i data-lucide="clock"></i>
                                <span>Sunday</span>
                                <span>Closed</span>
                            </li>
                        </ul>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <h4 class="font-playfair mb-4">Contact Info</h4>
                        <ul class="footer-contact">
                            <li>
                                <i data-lucide="map-pin"></i>
                                <span>123 Book Street, Reading City</span>
                            </li>
                            <li>
                                <i data-lucide="phone"></i>
                                <span>(555) 123-4567</span>
                            </li>
                            <li>
                                <i data-lucide="mail"></i>
                                <span>info@vintagelibrary.com</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer-bottom py-3">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-6 text-center text-md-start">
                        <p class="mb-0">&copy; 2024 Vintage Library. All rights reserved.</p>
                    </div>
                    <div class="col-md-6 text-center text-md-end">
                        <p class="mb-0">Designed with <i data-lucide="heart" class="text-danger mx-1"></i> for book lovers</p>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- In the scripts section, before closing body tag -->
    <script>
        // Initialize tooltips
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

        // Initialize Lucide icons
        lucide.createIcons();

        document.getElementById('searchForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const query = document.getElementById('searchInput').value.trim();
            if (query) {
                window.location.href = `search-results.php?q=${encodeURIComponent(query)}`;
            }
        });

        // Re-initialize icons after any dynamic content changes IF PHP generates new elements with data-lucide attributes
        // For a fully PHP rendered page, this might only be needed once on load.
        document.addEventListener('DOMContentLoaded', function() {
            lucide.createIcons();
            // updateFavoritesPanel(); // Removed as PHP handles this
        });

        // Function to update favorites panel - REMOVED
        // async function updateFavoritesPanel() { ... }

        // Function to remove a favorite - REMOVED
        // async function removeFavorite(bookId) { ... }

        // Function to add a favorite - REMOVED
        // async function addFavorite(book) { ... }

        // Toast notification function - REMOVED (consider PHP session messages for feedback)
        // function showToast(message) { ... }
        // function createToastContainer() { ... }
    </script>
    <?php
    // Close the database connection
    if (isset($conn)) {
        $conn->close();
    }
    ?>
</body>
</html>
