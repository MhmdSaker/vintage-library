<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact - Vintage Library</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Libre+Baskerville:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="src/styles/main.css">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        .hero-section {
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), 
                        url('src/images/vintage-books.jpg');
            background-size: cover;
            background-position: center;
            padding: 4rem 0;
            margin-bottom: 2rem;
        }

        .contact-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: transform 0.3s ease;
        }

        .contact-card:hover {
            transform: translateY(-5px);
        }

        .info-card {
            background: rgba(139, 115, 85, 0.1);
            border: 1px solid rgba(139, 115, 85, 0.2);
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .info-card:hover {
            background: rgba(139, 115, 85, 0.15);
            transform: translateY(-3px);
        }

        .info-card i {
            color: #8b7355;
            width: 24px;
            height: 24px;
        }

        .form-control {
            border: 1px solid #dcd7d0;
            padding: 0.75rem;
            border-radius: 6px;
            background-color: rgba(255, 255, 255, 0.9);
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #8b7355;
            box-shadow: 0 0 0 0.2rem rgba(139, 115, 85, 0.25);
        }

        .vintage-btn {
            background-color: #8b7355;
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 6px;
            transition: all 0.3s ease;
            font-family: 'Playfair Display', serif;
        }

        .vintage-btn:hover {
            background-color: #6d5a43;
            transform: translateY(-2px);
            color: white;
        }

        .hours-list li, .contact-list li {
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .hours-list i, .contact-list i {
            color: #8b7355;
            flex-shrink: 0;
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
    </style>
</head>
<body class="d-flex flex-column min-vh-100">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark custom-nav">
        <div class="container">
                        <a class="navbar-brand d-flex align-items-center" href="index.php">                <i data-lucide="library" class="me-2"></i>                <span class="font-playfair">Vintage Library</span>            </a>            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">                <span class="navbar-toggler-icon"></span>            </button>            <div class="collapse navbar-collapse" id="navbarNav">                <ul class="navbar-nav me-auto">                    <li class="nav-item">                        <a class="nav-link px-2" href="index.php" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Home">                            <i data-lucide="home"></i>                        </a>                    </li>                    <li class="nav-item">                        <a class="nav-link px-2" href="search-results.php" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Browse Books">                            <i data-lucide="book-open"></i>                        </a>                    </li>                    <li class="nav-item">                        <a class="nav-link px-2" href="categories.php" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Categories">                            <i data-lucide="list"></i>                        </a>                    </li>                    <li class="nav-item favorites-container">                        <a class="nav-link px-2 position-relative" href="#" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Favorites" data-bs-auto-close="outside">                            <i data-lucide="heart"></i>                            <span class="favorites-badge" id="favoritesCount">0</span>                        </a>                        <div class="favorites-panel">                            <div class="favorites-header">                                <h6 class="mb-0 font-playfair">Favorite Books</h6>                                <small class="text-muted">Recently Added</small>                            </div>                            <div id="favoritesList">                                <!-- Favorite items will be dynamically added here -->                            </div>                        </div>                    </li>                    <li class="nav-item">                        <a class="nav-link px-2" href="reading-list.php" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Reading List">                            <i data-lucide="list-checks"></i>                        </a>                    </li>                    <li class="nav-item">                        <a class="nav-link px-2" href="events.php" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Events">                            <i data-lucide="calendar"></i>                        </a>                    </li>                    <li class="nav-item">                        <a class="nav-link px-2" href="gallery.php" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Gallery">                            <i data-lucide="image"></i>                        </a>                    </li>                    <li class="nav-item">                        <a class="nav-link px-2" href="reviews.php" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Reviews">                            <i data-lucide="star"></i>                        </a>                    </li>                    <li class="nav-item">                        <a class="nav-link px-2" href="add-book.php" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Add Book">                            <i data-lucide="plus-circle"></i>                        </a>                    </li>                    <li class="nav-item">                        <a class="nav-link px-2" href="about.php" data-bs-toggle="tooltip" data-bs-placement="bottom" title="About">                            <i data-lucide="info"></i>                        </a>                    </li>                    <li class="nav-item active">                        <a class="nav-link px-2" href="contact.php" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Contact">                            <i data-lucide="mail"></i>                        </a>                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="hero-section text-center text-white">
        <div class="container">
            <h1 class="display-4 font-playfair mb-3">Contact Us</h1>
            <p class="lead">Get in touch with our friendly library staff</p>
        </div>
    </header>

    <main class="container py-4 flex-grow-1">
        <div class="row justify-content-center g-4">
            <div class="col-lg-8">
                <div class="contact-card p-5">
                    <div class="row mb-5">
                        <div class="col-md-6">
                            <div class="info-card p-4 h-100">
                                <h5 class="font-playfair mb-4">Library Hours</h5>
                                <ul class="hours-list list-unstyled mb-0">
                                    <li>
                                        <i data-lucide="clock"></i>
                                        <div>
                                            <strong>Monday - Friday</strong><br>
                                            9:00 AM - 8:00 PM
                                        </div>
                                    </li>
                                    <li>
                                        <i data-lucide="clock"></i>
                                        <div>
                                            <strong>Saturday</strong><br>
                                            10:00 AM - 6:00 PM
                                        </div>
                                    </li>
                                    <li>
                                        <i data-lucide="clock"></i>
                                        <div>
                                            <strong>Sunday</strong><br>
                                            Closed
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-card p-4 h-100">
                                <h5 class="font-playfair mb-4">Contact Info</h5>
                                <ul class="contact-list list-unstyled mb-0">
                                    <li>
                                        <i data-lucide="mail"></i>
                                        <div>
                                            <strong>Email</strong><br>
                                            info@vintagelibrary.com
                                        </div>
                                    </li>
                                    <li>
                                        <i data-lucide="phone"></i>
                                        <div>
                                            <strong>Phone</strong><br>
                                            (555) 123-4567
                                        </div>
                                    </li>
                                    <li>
                                        <i data-lucide="map-pin"></i>
                                        <div>
                                            <strong>Address</strong><br>
                                            123 Book Street, Reading City
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <form id="contactForm" class="needs-validation" novalidate>
                        <h4 class="font-playfair mb-4">Send us a Message</h4>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="name" placeholder="Your Name" required>
                                    <label for="name">Your Name</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="email" class="form-control" id="email" placeholder="Your Email" required>
                                    <label for="email">Your Email</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="subject" placeholder="Subject" required>
                                    <label for="subject">Subject</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating">
                                    <textarea class="form-control" id="message" placeholder="Your Message" style="height: 150px" required></textarea>
                                    <label for="message">Your Message</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn vintage-btn w-100">
                                    <i data-lucide="send" class="me-2"></i>
                                    Send Message
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <!-- Same footer as index.html -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="src/js/data.js"></script>
    <script src="src/js/main.js"></script>
    <script>
        // Initialize Lucide icons
        lucide.createIcons();

        // Initialize tooltips with updated options
        const tooltipOptions = {
            trigger: 'hover',
            placement: 'bottom',
            container: 'body'
        };
        
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => 
            new bootstrap.Tooltip(tooltipTriggerEl, tooltipOptions)
        );

        // Update favorites panel on load
        document.addEventListener('DOMContentLoaded', function() {
            updateFavoritesPanel();
        });

        // Function to update favorites panel
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
                console.error('Error updating favorites panel:', error);
                showToast('Error loading favorites');
            }
        }

        // Form handling
        document.getElementById('contactForm').addEventListener('submit', function(e) {
            e.preventDefault();
            if (this.checkValidity()) {
                showToast('Message sent successfully! We\'ll get back to you soon.');
                this.reset();
            }
            this.classList.add('was-validated');
        });

        // Toast notification
        function showToast(message) {
            const toastContainer = document.getElementById('toastContainer') || createToastContainer();
            const toast = document.createElement('div');
            toast.className = 'toast show';
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