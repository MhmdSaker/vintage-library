<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Book - Vintage Library</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Libre+Baskerville:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="src/styles/main.css">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        /* Add custom styles for the add book page */
        .hero-section {
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), 
                        url('src/images/vintage-books.jpg');
            background-size: cover;
            background-position: center;
            padding: 4rem 0;
            margin-bottom: 2rem;
        }

        .form-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            -webkit-backdrop-filter: blur(10px);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: transform 0.3s ease;
        }

        .form-card:hover {
            transform: translateY(-5px);
        }

        .form-label {
            font-family: 'Playfair Display', serif;
            color: #4a4a4a;
            margin-bottom: 0.5rem;
        }

        .form-control, .form-select {
            border: 1px solid #dcd7d0;
            padding: 0.75rem;
            border-radius: 6px;
            background-color: rgba(255, 255, 255, 0.9);
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: #8b7355;
        }

        /* Custom navbar styling */
        .custom-nav {
            background-color: #2c1810;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.85) !important;
            transition: all 0.3s ease;
            position: relative;
        }

        .nav-link:hover, .nav-link.active {
            color: white !important;
            transform: translateY(-2px);
        }

        .nav-link i {
            width: 18px;
            height: 18px;
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

        .toast.bg-danger {
            background-color: #dc3545 !important;
            color: white !important;
        }

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
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark custom-nav">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <i data-lucide="library" class="me-2"></i>
                <span class="font-playfair">Vintage Library</span>
            </a>
            <button title="toggle" class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
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
                        <a class="nav-link px-2" href="categories.php" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Categories">
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
                        <a class="nav-link px-2 active" href="add-book.php" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Add Book">
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
            <h1 class="display-4 font-playfair mb-3">Add New Book</h1>
            <p class="lead">Contribute to our growing collection of timeless literature</p>
        </div>
    </header>

    <main class="container py-4 flex-grow-1">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="form-card p-5">
                    <form id="addBookForm" class="needs-validation" novalidate>
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="title" name="title" placeholder="Title" required>
                                    <label for="title" class="required-field">Title</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="author" name="author" placeholder="Author" required>
                                    <label for="author" class="required-field">Author</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="isbn" name="isbn" placeholder="ISBN" required>
                                    <label for="isbn" class="required-field">ISBN</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select class="form-select" id="genre" name="genre" required>
                                        <option value="">Select Genre</option>
                                        <option value="Classic Romance">Classic Romance</option>
                                        <option value="Mystery">Mystery</option>
                                        <option value="Gothic Fiction">Gothic Fiction</option>
                                        <option value="Gothic Romance">Gothic Romance</option>
                                        <option value="Gothic Horror">Gothic Horror</option>
                                        <option value="Coming-of-age">Coming-of-age</option>
                                        <option value="Children's Literature">Children's Literature</option>
                                    </select>
                                    <label for="genre" class="required-field">Genre</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating">
                                    <textarea class="form-control" id="description" name="description" 
                                              placeholder="Description" style="height: 100px" required></textarea>
                                    <label for="description" class="required-field">Description</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="imageUrl" name="imageUrl" placeholder="Image URL">
                                    <label for="imageUrl">Image URL (optional)</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="number" class="form-control" id="copiesAvailable" name="copiesAvailable" placeholder="Copies Available" value="1">
                                    <label for="copiesAvailable">Copies Available</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="publicationDate" name="publicationDate" placeholder="Publication Date">
                                    <label for="publicationDate">Publication Date</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn vintage-btn w-100">
                                    <i data-lucide="plus" class="me-2"></i>
                                    Add Book to Collection
                                </button>
                            </div>
                            <div class="col-12 mt-3">
                                <a href="remove-books.php" class="btn btn-outline-danger w-100">
                                    <i data-lucide="trash-2" class="me-2"></i>
                                    Manage Books
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <!-- Same footer as index.html -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="src/js/db.js"></script>
    <script src="src/js/add-book.js"></script>
    <script>
        lucide.createIcons();
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize tooltips
            const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
            const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

            // Initialize Lucide icons
            lucide.createIcons();
        });
    </script>
</body>
</html> 