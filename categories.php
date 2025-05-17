<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Categories - Vintage Library</title>
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

        .hero-section {
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), 
                        url('src/images/vintage-books.jpg');
            background-size: cover;
            background-position: center;
            padding: 4rem 0;
            margin-bottom: 2rem;
        }

        .category-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
            height: 100%;
        }

        .category-card:hover {
            transform: translateY(-5px);
        }

        .category-icon {
            width: 64px;
            height: 64px;
            color: #8b7355;
            margin-bottom: 1.5rem;
        }

        .category-count {
            background: rgba(139, 115, 85, 0.1);
            color: #8b7355;
            padding: 0.25rem 1rem;
            border-radius: 20px;
            font-size: 0.875rem;
            margin-bottom: 1rem;
            display: inline-block;
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

        /* Favorites Panel Styles */
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
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
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
                        <a class="nav-link px-2" href="search-results.php" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Browse Books">
                            <i data-lucide="book-open"></i>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-2 active" href="categories.php" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Categories">
                            <i data-lucide="list"></i>
                        </a>
                    </li>
                    <li class="nav-item favorites-container">
                        <a class="nav-link px-2 position-relative" href="#" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Favorites">
                            <i data-lucide="heart"></i>
                            <span class="favorites-badge" id="favoritesCount">0</span>
                        </a>
                        <div class="favorites-panel">
                            <div class="favorites-header">
                                <h6 class="mb-0 font-playfair">Favorite Books</h6>
                                <small class="text-muted">Recently Added</small>
                            </div>
                            <div id="favoritesList">
                                <!-- Favorite items will be dynamically added here -->
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

    <!-- Hero Section -->
    <header class="hero-section text-center text-white">
        <div class="container">
            <h1 class="display-4 font-playfair mb-3">Book Categories</h1>
            <p class="lead">Explore our collection by genre</p>
        </div>
    </header>

    <main class="container py-4 flex-grow-1">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="category-card p-4 text-center">
                    <i data-lucide="heart" class="category-icon"></i>
                    <span class="category-count">24 Books</span>
                    <h3 class="font-playfair h4 mb-3">Classic Romance</h3>
                    <p class="text-muted mb-4">Timeless love stories that have captured hearts for generations</p>
                    <button class="btn vintage-btn w-100" onclick="filterByGenre('Classic Romance')">
                        Browse Collection
                    </button>
                </div>
            </div>
            <div class="col-md-4">
                <div class="category-card p-4 text-center">
                    <i data-lucide="skull" class="category-icon"></i>
                    <span class="category-count">18 Books</span>
                    <h3 class="font-playfair h4 mb-3">Gothic Fiction</h3>
                    <p class="text-muted mb-4">Dark and mysterious tales of romance and horror</p>
                    <button class="btn vintage-btn w-100" onclick="filterByGenre('Gothic Fiction')">
                        Browse Collection
                    </button>
                </div>
            </div>
            <div class="col-md-4">
                <div class="category-card p-4 text-center">
                    <i data-lucide="search" class="category-icon"></i>
                    <span class="category-count">15 Books</span>
                    <h3 class="font-playfair h4 mb-3">Mystery</h3>
                    <p class="text-muted mb-4">Intriguing detective stories and puzzling mysteries</p>
                    <button class="btn vintage-btn w-100" onclick="filterByGenre('Mystery')">
                        Browse Collection
                    </button>
                </div>
            </div>
            <div class="col-md-4">
                <div class="category-card p-4 text-center">
                    <i data-lucide="ghost" class="category-icon"></i>
                    <span class="category-count">12 Books</span>
                    <h3 class="font-playfair h4 mb-3">Gothic Horror</h3>
                    <p class="text-muted mb-4">Spine-chilling tales of supernatural and psychological horror</p>
                    <button class="btn vintage-btn w-100" onclick="filterByGenre('Gothic Horror')">
                        Browse Collection
                    </button>
                </div>
            </div>
            <div class="col-md-4">
                <div class="category-card p-4 text-center">
                    <i data-lucide="sprout" class="category-icon"></i>
                    <span class="category-count">20 Books</span>
                    <h3 class="font-playfair h4 mb-3">Coming-of-age</h3>
                    <p class="text-muted mb-4">Stories of growth, self-discovery, and life lessons</p>
                    <button class="btn vintage-btn w-100" onclick="filterByGenre('Coming-of-age')">
                        Browse Collection
                    </button>
                </div>
            </div>
            <div class="col-md-4">
                <div class="category-card p-4 text-center">
                    <i data-lucide="baby" class="category-icon"></i>
                    <span class="category-count">16 Books</span>
                    <h3 class="font-playfair h4 mb-3">Children's Literature</h3>
                    <p class="text-muted mb-4">Classic tales that have delighted young readers for centuries</p>
                    <button class="btn vintage-btn w-100" onclick="filterByGenre('Children\'s Literature')">
                        Browse Collection
                    </button>
                </div>
            </div>
        </div>
    </main>

    <div class="toast-container"></div>

    <!-- Same footer as index.html -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="src/js/data.js"></script>
    <script src="src/js/main.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', async function() {
            // Initialize tooltips
            const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
            const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

            // Initialize Lucide icons
            lucide.createIcons();

            async function updateFavoritesPanel() {
                try {
                    const response = await fetch('src/api/favorites.php');
                    const data = await response.json();
                    
                    if (!data.success) {
                        throw new Error(data.message || 'Failed to load favorites');
                    }

                    const favorites = data.favorites;
                    const favoritesList = document.getElementById('favoritesList');
                    const favoritesCount = document.getElementById('favoritesCount');
                    
                    // Update badge count
                    favoritesCount.textContent = favorites.length;
                    
                    if (favorites.length === 0) {
                        favoritesList.innerHTML = `
                            <div class="favorites-empty">
                                <i data-lucide="heart-off" class="mb-2"></i>
                                <p class="mb-0">No favorite books yet</p>
                            </div>
                        `;
                        lucide.createIcons();
                        return;
                    }

                    favoritesList.innerHTML = favorites
                        .slice(0, 5) // Show only last 5 favorites
                        .map(book => `
                            <div class="favorite-item">
                                <img src="${book.imageUrl}" alt="${book.title}">
                                <div class="favorite-item-info">
                                    <h6 class="favorite-item-title">${book.title}</h6>
                                    <p class="favorite-item-author">${book.author}</p>
                                </div>
                                <button class="favorite-item-remove" onclick="removeFavorite('${book.id}')">
                                    <i data-lucide="x"></i>
                                </button>
                            </div>
                        `).join('');

                    lucide.createIcons();
                } catch (error) {
                    console.error('Error loading favorites:', error);
                    showToast('Error loading favorites');
                }
            }

            // Initial load
            updateFavoritesPanel();

            // Remove favorite function
            window.removeFavorite = async function(bookId) {
                try {
                    const response = await fetch('src/api/favorites.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            bookId: bookId,
                            action: 'remove'
                        })
                    });

                    const data = await response.json();
                    
                    if (!data.success) {
                        throw new Error(data.message || 'Failed to remove favorite');
                    }

                    updateFavoritesPanel();
                    showToast('Book removed from favorites');
                } catch (error) {
                    console.error('Error removing favorite:', error);
                    showToast('Error removing book from favorites');
                }
            };
        });

        function filterByGenre(genre) {
            // Store the selected genre in localStorage
            localStorage.setItem('selectedGenre', genre);
            // Redirect to index page with filtered results
            window.location.href = 'index.php?genre=' + encodeURIComponent(genre);
        }
    </script>
</body>
</html> 