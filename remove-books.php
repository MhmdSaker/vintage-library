<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Remove Books - Vintage Library</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Libre+Baskerville:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="src/styles/main.css">
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
    <style>
        /* Update navbar styles to match index.html exactly */
        .custom-nav {
            background-color: #2d2d2d;
            padding: 0.5rem 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .navbar-brand {
            font-size: 1.5rem;
            color: white !important;
            padding: 0.5rem 1rem;
        }

        .nav-item {
            width: auto !important;
            display: flex;
            align-items: center;
            margin: 0 0.25rem;
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.8) !important;
            transition: all 0.3s ease;
            border-radius: 6px;
            padding: 0.5rem 0.75rem !important;
            width: auto !important;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .nav-link:hover {
            color: white !important;
            background-color: rgba(255, 255, 255, 0.1);
        }

        .nav-link.active {
            color: white !important;
            background-color: rgba(255, 255, 255, 0.2);
        }

        /* Add missing styles for icons */
        .nav-link i,
        .nav-link svg {
            width: 24px !important;
            height: 24px !important;
            stroke-width: 2px !important;
        }

        .navbar-brand i,
        .navbar-brand svg {
            width: 28px !important;
            height: 28px !important;
            stroke-width: 2px !important;
        }

        /* Add padding to nav links for better spacing */
        .nav-link {
            padding: 0.75rem 1.25rem !important;
        }

        /* Add the same styles as index.html */
        .book-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            height: 100%;
            overflow: hidden;
            position: relative;
            z-index: 1;
        }

        .book-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 20px rgba(139, 115, 85, 0.15);
            z-index: 2;
        }

        .book-image {
            width: 100%;
            height: 250px;
            object-fit: cover;
            position: relative;
        }

        .book-info {
            padding: 1.5rem;
        }

        .book-title {
            font-family: 'Playfair Display', serif;
            margin-bottom: 0.5rem;
            color: #2d2d2d;
        }

        .book-author {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }

        .book-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: #8b7355;
            font-size: 0.875rem;
            margin-top: auto;
        }

        .remove-btn {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            transition: all 0.3s ease;
            width: 100%;
            margin-top: 1rem;
        }

        .remove-btn:hover {
            background-color: #bb2d3b;
            transform: translateY(-2px);
        }

        .hero-section {
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), 
                        url('src/images/vintage-books.jpg');
            background-size: cover;
            background-position: center;
            padding: 4rem 0;
            margin-bottom: 2rem;
            color: white;
            position: relative;
            z-index: 2;
        }

        /* Add the favorites panel styles */
        /* ... (copy the favorites panel styles from index.html) ... */

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

        /* Toast Styles */
        .toast-container {
            z-index: 1050;
        }

        .toast {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            margin-bottom: 0.5rem;
        }

        .toast-body {
            padding: 0.75rem 1rem;
            color: #2d2d2d;
        }

        .toast.bg-danger {
            background-color: #dc3545 !important;
            color: white !important;
        }

        /* Add a container style for the main content */
        main.container {
            position: relative;
            z-index: 1;
        }

        .hero-section {
            position: relative;
            z-index: 2;
        }
    </style>
</head>
<body>
    <!-- Update the navbar structure to match index.html exactly -->
    <nav class="navbar navbar-expand-lg navbar-dark custom-nav">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="index.html">
                <i data-lucide="library" class="me-2"></i>
                <span class="font-playfair">Vintage Library</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-label="Toggle navigation" title="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link px-2" href="index.html" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Home">
                            <i data-lucide="home"></i>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-2" href="search-results.html" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Browse Books">
                            <i data-lucide="book-open"></i>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-2" href="categories.html" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Categories">
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
                        <a class="nav-link px-2" href="reading-list.html" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Reading List">
                            <i data-lucide="list-checks"></i>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-2" href="events.html" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Events">
                            <i data-lucide="calendar"></i>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-2" href="gallery.html" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Gallery">
                            <i data-lucide="image"></i>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-2" href="reviews.html" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Reviews">
                            <i data-lucide="star"></i>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-2" href="add-book.html" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Add Book">
                            <i data-lucide="plus-circle"></i>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-2 active" href="remove-books.html" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Remove Books">
                            <i data-lucide="trash-2"></i>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-2" href="about.html" data-bs-toggle="tooltip" data-bs-placement="bottom" title="About">
                            <i data-lucide="info"></i>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-2" href="contact.html" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Contact">
                            <i data-lucide="mail"></i>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="hero-section text-center">
        <div class="container">
            <h1 class="display-4 font-playfair mb-3">Remove Books</h1>
            <p class="lead">Manage your library collection</p>
        </div>
    </header>

    <main class="container py-4">
        <div class="row g-4" id="booksGrid">
            <!-- Books will be dynamically added here -->
        </div>
    </main>

    <!-- Copy the footer from index.html -->
    <!-- ... footer code ... -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="src/js/data.js"></script>
    <script src="src/js/favorites.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize tooltips
            const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
            const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

            // Initialize Lucide icons
            lucide.createIcons();

            // Load books
            loadBooks();
        });

        function showLoading() {
            const booksGrid = document.getElementById('booksGrid');
            booksGrid.innerHTML = `
                <div class="col-12 text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Loading books...</p>
                </div>
            `;
        }

        async function loadBooks() {
            showLoading();
            try {
                const response = await fetch('src/api/books.php');
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const data = await response.json();
                
                if (!data.success) {
                    throw new Error(data.error || 'Failed to load books');
                }
                
                if (!data || !data.books) {
                    throw new Error('Invalid data format');
                }
                
                const booksGrid = document.getElementById('booksGrid');
                
                if (data.books.length === 0) {
                    booksGrid.innerHTML = `
                        <div class="col-12 text-center">
                            <h3>No books found</h3>
                            <p>The library is currently empty.</p>
                        </div>
                    `;
                    return;
                }
                
                booksGrid.innerHTML = data.books.map(book => `
                    <div class="col-sm-6 col-md-4 col-lg-3 mb-4">
                        <div class="book-card h-100">
                            <img src="${book.imageUrl || 'src/images/default-book.jpg'}" alt="${book.title}" class="book-image">
                            <div class="book-info d-flex flex-column">
                                <h5 class="book-title">${book.title}</h5>
                                <p class="book-author">by ${book.author}</p>
                                <div class="book-meta mt-auto">
                                    <span>${book.genre}</span>
                                    <span>${book.copiesAvailable} copies</span>
                                </div>
                                <button class="remove-btn mt-3" onclick="removeBook('${book.id}', '${book.title.replace(/'/g, "\\'")}')">
                                    <i data-lucide="trash-2" class="me-2"></i>
                                    Remove Book
                                </button>
                            </div>
                        </div>
                    </div>
                `).join('');

                lucide.createIcons();
            } catch (error) {
                console.error('Error loading books:', error);
                showToast('Error loading books: ' + error.message, true);
                const booksGrid = document.getElementById('booksGrid');
                booksGrid.innerHTML = `
                    <div class="col-12 text-center text-danger">
                        <i data-lucide="alert-circle" style="width: 48px; height: 48px;"></i>
                        <p class="mt-2">Failed to load books. Please try refreshing the page.</p>
                    </div>
                `;
                lucide.createIcons();
            }
        }

        async function removeBook(bookId, bookTitle) {
            if (!confirm(`Are you sure you want to remove "${bookTitle}"?`)) {
                return;
            }

            try {
                const response = await fetch(`src/api/books.php?id=${bookId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json'
                    }
                });

                const data = await response.json();
                
                if (!data.success) {
                    throw new Error(data.message || 'Failed to remove book');
                }

                showToast('Book removed successfully');
                loadBooks(); // Reload the books grid
            } catch (error) {
                console.error('Error removing book:', error);
                showToast(error.message || 'Error removing book', true);
            }
        }

        function showToast(message, isError = false) {
            const toastContainer = document.getElementById('toastContainer') || createToastContainer();
            const toast = document.createElement('div');
            toast.className = `toast show ${isError ? 'bg-danger text-white' : ''}`;
            toast.innerHTML = `
                <div class="toast-body">
                    ${message}
                </div>
            `;
            toastContainer.appendChild(toast);
            setTimeout(() => {
                toast.remove();
            }, 3000);
        }

        function createToastContainer() {
            const container = document.createElement('div');
            container.id = 'toastContainer';
            container.className = 'toast-container position-fixed bottom-0 end-0 p-3';
            document.body.appendChild(container);
            return container;
        }
    </script>
</body>
</html> 