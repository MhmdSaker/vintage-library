<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Reviews - Vintage Library</title>
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

        .reviews-filter select {
            background-color: rgba(255, 255, 255, 0.9);
            border: 1px solid #dcd7d0;
            min-width: 150px;
        }

        .review-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: 1px solid #dcd7d0;
        }

        .review-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .review-avatar {
            width: 48px;
            height: 48px;
            background: #f8f9fa;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .rating {
            color: #ffd700;
            font-size: 1.1rem;
            letter-spacing: 2px;
        }

        .rating-input {
            display: flex;
            flex-direction: row-reverse;
            gap: 0.5rem;
        }

        .rating-input input {
            display: none;
        }

        .rating-input label {
            color: #dcd7d0;
            font-size: 1.5rem;
            cursor: pointer;
            transition: color 0.2s ease;
        }

        .rating-input label:hover,
        .rating-input label:hover ~ label,
        .rating-input input:checked ~ label {
            color: #ffd700;
        }

        .sticky-top {
            top: 2rem;
            z-index: 1000;
        }

        .review-meta {
            font-size: 0.875rem;
            color: #6c757d;
        }

        .review-text {
            line-height: 1.6;
            color: #4a4a4a;
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark custom-nav">
        <div class="container">
                        <a class="navbar-brand d-flex align-items-center" href="index.php">                <i data-lucide="library" class="me-2"></i>                <span class="font-playfair">Vintage Library</span>            </a>            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-label="Toggle navigation">                <span class="navbar-toggler-icon"></span>            </button>            <div class="collapse navbar-collapse" id="navbarNav">                <ul class="navbar-nav me-auto">                    <li class="nav-item">                        <a class="nav-link px-2" href="index.php" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Home" aria-label="Home">                            <i data-lucide="home" aria-hidden="true"></i>                        </a>                    </li>                    <li class="nav-item">                        <a class="nav-link px-2" href="search-results.php" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Browse Books" aria-label="Browse Books">                            <i data-lucide="book-open" aria-hidden="true"></i>                        </a>                    </li>                    <li class="nav-item">                        <a class="nav-link px-2" href="categories.php" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Categories" aria-label="Categories">                            <i data-lucide="list" aria-hidden="true"></i>                        </a>                    </li>                    <li class="nav-item">                        <a class="nav-link px-2" href="index.php" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Favorites" aria-label="Favorites">                            <i data-lucide="heart" aria-hidden="true"></i>                        </a>                    </li>                    <li class="nav-item">                        <a class="nav-link px-2" href="reading-list.php" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Reading List" aria-label="Reading List">                            <i data-lucide="list-checks" aria-hidden="true"></i>                        </a>                    </li>                    <li class="nav-item">                        <a class="nav-link px-2" href="events.php" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Events" aria-label="Events">                            <i data-lucide="calendar" aria-hidden="true"></i>                        </a>                    </li>                    <li class="nav-item">                        <a class="nav-link px-2" href="gallery.php" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Gallery" aria-label="Gallery">                            <i data-lucide="image" aria-hidden="true"></i>                        </a>                    </li>                    <li class="nav-item">                        <a class="nav-link px-2 active" href="reviews.php" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Reviews" aria-label="Reviews">                            <i data-lucide="star" aria-hidden="true"></i>                        </a>                    </li>                    <li class="nav-item">                        <a class="nav-link px-2" href="add-book.php" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Add Book" aria-label="Add Book">                            <i data-lucide="plus-circle" aria-hidden="true"></i>                        </a>                    </li>                    <li class="nav-item">                        <a class="nav-link px-2" href="about.php" data-bs-toggle="tooltip" data-bs-placement="bottom" title="About" aria-label="About">                            <i data-lucide="info" aria-hidden="true"></i>                        </a>                    </li>                    <li class="nav-item">                        <a class="nav-link px-2" href="contact.php" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Contact" aria-label="Contact">                            <i data-lucide="mail" aria-hidden="true"></i>                        </a>                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="py-5 text-center text-white" style="background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('src/images/vintage-books.jpg'); background-size: cover; background-position: center;">
        <div class="container">
            <h1 class="display-4 font-playfair mb-3">Community Reviews</h1>
            <p class="lead">Share your thoughts and discover what others are reading</p>
        </div>
    </header>

    <main class="container py-5 flex-grow-1">
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="vintage-card p-4">
                    <div class="reviews-filter mb-4">
                        <div class="d-flex flex-wrap gap-3 align-items-center">
                            <select class="form-select" aria-label="Filter reviews by book">
                                <option value="">All Books</option>
                            </select>
                            <select class="form-select" aria-label="Sort reviews">
                                <option>Latest First</option>
                                <option>Highest Rated</option>
                                <option>Lowest Rated</option>
                            </select>
                        </div>
                    </div>
                    <div id="reviewsList">
                        <!-- Reviews will be dynamically loaded here -->
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="vintage-card p-4 sticky-top">
                    <h3 class="font-playfair mb-4">Write a Review</h3>
                    <form id="reviewForm">
                        <div class="mb-3">
                            <label class="form-label">Select Book</label>
                            <select class="form-select" id="bookSelect" required aria-label="Select a book to review">
                                <option value="">Choose a book...</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Your Name</label>
                            <input type="text" class="form-control" id="reviewerName" required placeholder="Enter your name" aria-label="Your name">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Rating</label>
                            <div class="rating-input">
                                <input type="radio" id="star5" name="rating" value="5" required>
                                <label for="star5">★</label>
                                <input type="radio" id="star4" name="rating" value="4">
                                <label for="star4">★</label>
                                <input type="radio" id="star3" name="rating" value="3">
                                <label for="star3">★</label>
                                <input type="radio" id="star2" name="rating" value="2">
                                <label for="star2">★</label>
                                <input type="radio" id="star1" name="rating" value="1">
                                <label for="star1">★</label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Your Review</label>
                            <textarea class="form-control" id="reviewText" rows="4" required aria-label="Your review text"></textarea>
                        </div>
                        <button type="submit" class="btn vintage-btn w-100" aria-label="Submit Review">
                            <i data-lucide="send" class="me-2" aria-hidden="true"></i>
                            Submit Review
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <!-- Same footer -->

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="src/js/db.js"></script>
    <script src="src/js/auth.js"></script>
    <script src="src/js/reviews.js"></script>
    <script>
        // Initialize Lucide icons
        lucide.createIcons();
    </script>
</body>
</html>