<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery - Vintage Library</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Libre+Baskerville:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="src/styles/main.css">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        .bento-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            grid-auto-rows: 200px;
            gap: 1rem;
            padding: 2rem 0;
        }

        .bento-item {
            position: relative;
            border-radius: 1rem;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .bento-item:hover {
            transform: scale(1.02);
        }

        .bento-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: all 0.3s ease;
        }

        .bento-item:hover img {
            transform: scale(1.1);
        }

        .bento-caption {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 1rem;
            background: linear-gradient(transparent, rgba(0,0,0,0.8));
            color: white;
            opacity: 0;
            transition: all 0.3s ease;
        }

        .bento-item:hover .bento-caption {
            opacity: 1;
        }

        /* Bento grid layout variations */
        .wide {
            grid-column: span 2;
        }

        .tall {
            grid-row: span 2;
        }

        .big {
            grid-column: span 2;
            grid-row: span 2;
        }

        .hero {
            grid-column: span 4;
            grid-row: span 2;
        }

        /* Page title styling */
        .gallery-title {
            text-align: center;
            padding: 3rem 0;
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('https://images.pexels.com/photos/1290141/pexels-photo-1290141.jpeg');
            background-size: cover;
            background-position: center;
            color: white;
            margin-bottom: 2rem;
        }

        .gallery-title h1 {
            font-family: 'Playfair Display', serif;
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        .gallery-title p {
            font-family: 'Libre Baskerville', serif;
            font-size: 1.2rem;
            max-width: 600px;
            margin: 0 auto;
        }

        /* Responsive adjustments */
        @media (max-width: 992px) {
            .bento-grid {
                grid-template-columns: repeat(3, 1fr);
            }
            .hero {
                grid-column: span 3;
            }
        }

        @media (max-width: 768px) {
            .bento-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .hero {
                grid-column: span 2;
            }
        }

        @media (max-width: 576px) {
            .bento-grid {
                grid-template-columns: 1fr;
                grid-auto-rows: 250px;
            }
            .hero, .wide, .tall, .big {
                grid-column: span 1;
                grid-row: span 1;
            }
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
                        <a class="nav-link px-2 active" href="gallery.php" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Gallery">
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

    <!-- Gallery Title Section -->
    <div class="gallery-title">
        <div class="container">
            <h1>Library Gallery</h1>
            <p>Explore the beauty and tranquility of our vintage library through this visual journey</p>
        </div>
    </div>

    <!-- Gallery Grid -->
    <div class="container">
        <div class="bento-grid">
            <div class="bento-item"><img src="https://images.pexels.com/photos/1907785/pexels-photo-1907785.jpeg?auto=compress&cs=tinysrgb&w=600" alt="Vintage Library 1"></div>
            <div class="bento-item"><img src="https://images.pexels.com/photos/1907784/pexels-photo-1907784.jpeg?auto=compress&cs=tinysrgb&w=600" alt="Vintage Library 2"></div>
            <div class="bento-item"><img src="https://images.pexels.com/photos/2079451/pexels-photo-2079451.jpeg?auto=compress&cs=tinysrgb&w=600" alt="Vintage Library 3"></div>
            <div class="bento-item"><img src="https://images.pexels.com/photos/1926988/pexels-photo-1926988.jpeg?auto=compress&cs=tinysrgb&w=600" alt="Vintage Library 4"></div>
            <div class="bento-item"><img src="https://images.pexels.com/photos/1850022/pexels-photo-1850022.jpeg?auto=compress&cs=tinysrgb&w=600" alt="Vintage Library 5"></div>
            <div class="bento-item"><img src="https://images.pexels.com/photos/2128249/pexels-photo-2128249.jpeg?auto=compress&cs=tinysrgb&w=600" alt="Vintage Library 6"></div>
            <div class="bento-item"><img src="https://images.pexels.com/photos/3747505/pexels-photo-3747505.jpeg?auto=compress&cs=tinysrgb&w=600" alt="Vintage Library 7"></div>
            <div class="bento-item"><img src="https://images.pexels.com/photos/1907786/pexels-photo-1907786.jpeg?auto=compress&cs=tinysrgb&w=600" alt="Vintage Library 8"></div>
            <div class="bento-item"><img src="https://images.pexels.com/photos/3995842/pexels-photo-3995842.jpeg?auto=compress&cs=tinysrgb&w=600" alt="Vintage Library 9"></div>
            <div class="bento-item"><img src="https://images.pexels.com/photos/3747503/pexels-photo-3747503.jpeg?auto=compress&cs=tinysrgb&w=600" alt="Vintage Library 10"></div>
            <div class="bento-item"><img src="https://images.pexels.com/photos/3747517/pexels-photo-3747517.jpeg?auto=compress&cs=tinysrgb&w=600" alt="Vintage Library 11"></div>
            <div class="bento-item"><img src="https://images.pexels.com/photos/4916244/pexels-photo-4916244.jpeg?auto=compress&cs=tinysrgb&w=600" alt="Vintage Library 12"></div>
            <div class="bento-item"><img src="https://images.pexels.com/photos/29593734/pexels-photo-29593734/free-photo-of-charming-european-bookshop-with-postcards-display.jpeg?auto=compress&cs=tinysrgb&w=600" alt="Vintage Library 13"></div>
            <div class="bento-item"><img src="https://images.pexels.com/photos/29589095/pexels-photo-29589095/free-photo-of-cozy-bookstore-with-wooden-ceiling-interior.jpeg?auto=compress&cs=tinysrgb&w=600" alt="Vintage Library 14"></div>
            <div class="bento-item"><img src="https://images.pexels.com/photos/29586775/pexels-photo-29586775/free-photo-of-cozy-library-aisle-with-books-and-ladder.jpeg?auto=compress&cs=tinysrgb&w=600" alt="Vintage Library 15"></div>
            <div class="bento-item"><img src="https://images.pexels.com/photos/29545914/pexels-photo-29545914/free-photo-of-library-aisle-with-sunlit-bookshelves-in-talgar.jpeg?auto=compress&cs=tinysrgb&w=600" alt="Vintage Library 16"></div>
            <div class="bento-item"><img src="https://images.pexels.com/photos/28957745/pexels-photo-28957745/free-photo-of-cozy-vintage-library-corner-with-armchair.jpeg?auto=compress&cs=tinysrgb&w=600" alt="Vintage Library 17"></div>
            <div class="bento-item"><img src="https://images.pexels.com/photos/8798231/pexels-photo-8798231.jpeg?auto=compress&cs=tinysrgb&w=600" alt="Vintage Library 18"></div>
            <div class="bento-item"><img src="https://images.pexels.com/photos/1290141/pexels-photo-1290141.jpeg?auto=compress&cs=tinysrgb&w=600" alt="Vintage Library 19"></div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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