<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Library Events - Vintage Library</title>
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <link
      href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Libre+Baskerville:wght@400;700&display=swap"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="src/styles/main.css" />
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
      .hero-section {
        background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)),
          url("src/images/vintage-books.jpg");
        background-size: cover;
        background-position: center;
        padding: 4rem 0;
        margin-bottom: 2rem;
      }

      .event-card {
        background: rgba(255, 255, 255, 0.95);
        border-radius: 12px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
      }

      .event-card:hover {
        transform: translateY(-5px);
      }

      .date-badge {
        position: absolute;
        top: 1rem;
        right: 1rem;
        background: #8b7355;
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 6px;
        font-weight: 500;
        font-family: "Playfair Display", serif;
      }

      .event-info {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #6c757d;
        font-size: 0.875rem;
        margin-bottom: 0.5rem;
      }

      .event-info i {
        color: #8b7355;
        width: 18px;
        height: 18px;
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
      #favoritesPanel {
        position: fixed;
        top: 0;
        right: -400px;
        width: 400px;
        height: 100vh;
        background-color: white;
        box-shadow: -2px 0 5px rgba(0, 0, 0, 0.2);
        transition: right 0.3s ease;
        z-index: 1000;
        overflow-y: auto;
        padding: 20px;
      }

      #favoritesPanel.show {
        right: 0;
      }

      .favorites-header {
        background-color: #8b7355;
        color: white;
        padding: 1rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin: -20px -20px 20px -20px;
      }

      .favorite-item {
        padding: 1rem;
        border-bottom: 1px solid #eee;
        display: flex;
        align-items: start;
        gap: 1rem;
      }

      .favorite-item img {
        width: 60px;
        height: 90px;
        object-fit: cover;
        border-radius: 4px;
      }

      .favorite-item-details {
        flex: 1;
      }

      .favorite-item-details h6 {
        margin: 0;
        font-size: 0.9rem;
        color: #2c1810;
      }

      .favorite-item-details p {
        margin: 5px 0;
        font-size: 0.8rem;
        color: #666;
      }

      .remove-favorite {
        background: none;
        border: none;
        color: #dc3545;
        cursor: pointer;
        padding: 0;
        font-size: 0.8rem;
      }

      .remove-favorite:hover {
        color: #bb2d3b;
      }

      .close-favorites {
        background: none;
        border: none;
        color: white;
        cursor: pointer;
      }

      .favorites-badge {
        position: absolute;
        top: -5px;
        right: -5px;
        background-color: #dc3545;
        color: white;
        border-radius: 50%;
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
        min-width: 1.5rem;
        text-align: center;
      }

      .empty-favorites {
        text-align: center;
        padding: 2rem;
        color: #666;
      }
    </style>
  </head>
  <body class="d-flex flex-column min-vh-100">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark custom-nav">
      <div class="container">
                <a class="navbar-brand d-flex align-items-center" href="index.php">          <i data-lucide="library" class="me-2"></i>          <span class="font-playfair">Vintage Library</span>        </a>        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">          <span class="navbar-toggler-icon"></span>        </button>        <div class="collapse navbar-collapse" id="navbarNav">          <ul class="navbar-nav me-auto">            <li class="nav-item">              <a class="nav-link px-2" href="index.php" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Home">                <i data-lucide="home"></i>              </a>            </li>            <li class="nav-item">              <a class="nav-link px-2" href="search-results.php" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Browse Books">                <i data-lucide="book-open"></i>              </a>            </li>            <li class="nav-item">              <a class="nav-link px-2" href="categories.php" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Categories">                <i data-lucide="list"></i>              </a>            </li>            <li class="nav-item">              <a class="nav-link px-2" href="#" id="favoritesBtn" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Favorites">                <i data-lucide="heart"></i>                <span class="favorites-badge" id="favoritesCount">0</span>              </a>            </li>            <li class="nav-item">              <a class="nav-link px-2" href="reading-list.php" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Reading List">                <i data-lucide="list-checks"></i>              </a>            </li>            <li class="nav-item">              <a class="nav-link px-2 active" href="events.php" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Events">                <i data-lucide="calendar"></i>              </a>            </li>            <li class="nav-item">              <a class="nav-link px-2" href="gallery.php" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Gallery">                <i data-lucide="image"></i>              </a>            </li>            <li class="nav-item">              <a class="nav-link px-2" href="reviews.php" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Reviews">                <i data-lucide="star"></i>              </a>            </li>            <li class="nav-item">              <a class="nav-link px-2" href="add-book.php" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Add Book">                <i data-lucide="plus-circle"></i>              </a>            </li>            <li class="nav-item">              <a class="nav-link px-2" href="about.php" data-bs-toggle="tooltip" data-bs-placement="bottom" title="About">                <i data-lucide="info"></i>              </a>            </li>            <li class="nav-item">              <a class="nav-link px-2" href="contact.php" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Contact">                <i data-lucide="mail"></i>              </a>            </li>
          </ul>
        </div>
      </div>
    </nav>

    <!-- Favorites Panel -->
    <div id="favoritesPanel">
      <div class="favorites-header">
        <h5 class="m-0">Favorite Books</h5>
        <button class="close-favorites" id="closeFavorites">
          <i data-lucide="x"></i>
        </button>
      </div>
      <div id="favoritesList"></div>
    </div>

    <!-- Hero Section -->
    <header class="hero-section text-center text-white">
      <div class="container">
        <h1 class="display-4 font-playfair mb-3">Library Events</h1>
        <p class="lead">Join our community of book lovers</p>
      </div>
    </header>

    <main class="container py-4 flex-grow-1">
      <div class="row g-4">
        <div class="col-md-6 col-lg-4">
          <div class="event-card p-4">
            <div class="date-badge">MAR 15</div>
            <h3 class="font-playfair h5 mb-3">Book Club Meeting</h3>
            <div class="event-info">
              <i data-lucide="book-open"></i>
              <span>Pride and Prejudice Discussion</span>
            </div>
            <div class="event-info">
              <i data-lucide="clock"></i>
              <span>2:00 PM - 4:00 PM</span>
            </div>
            <div class="event-info mb-4">
              <i data-lucide="map-pin"></i>
              <span>Main Reading Room</span>
            </div>
            <p class="text-muted mb-4">
              Join us for an engaging discussion of Jane Austen's masterpiece.
            </p>
            <button class="btn vintage-btn w-100">Join Event</button>
          </div>
        </div>

        <div class="col-md-6 col-lg-4">
          <div class="event-card p-4">
            <div class="date-badge">MAR 20</div>
            <h3 class="font-playfair h5 mb-3">Author Meet & Greet</h3>
            <div class="event-info">
              <i data-lucide="users"></i>
              <span>Special Guest: Sarah Mitchell</span>
            </div>
            <div class="event-info">
              <i data-lucide="clock"></i>
              <span>6:00 PM - 8:00 PM</span>
            </div>
            <div class="event-info mb-4">
              <i data-lucide="map-pin"></i>
              <span>Events Hall</span>
            </div>
            <p class="text-muted mb-4">
              Meet the author of "The Lost Letters" and get your book signed.
            </p>
            <button class="btn vintage-btn w-100">Reserve Spot</button>
          </div>
        </div>

        <div class="col-md-6 col-lg-4">
          <div class="event-card p-4">
            <div class="date-badge">MAR 25</div>
            <h3 class="font-playfair h5 mb-3">Poetry Reading Night</h3>
            <div class="event-info">
              <i data-lucide="mic"></i>
              <span>Victorian Poetry Special</span>
            </div>
            <div class="event-info">
              <i data-lucide="clock"></i>
              <span>7:00 PM - 9:00 PM</span>
            </div>
            <div class="event-info mb-4">
              <i data-lucide="map-pin"></i>
              <span>Poetry Corner</span>
            </div>
            <p class="text-muted mb-4">
              An evening of classic Victorian poetry readings and discussion.
            </p>
            <button class="btn vintage-btn w-100">Register Now</button>
          </div>
        </div>
      </div>
    </main>

    <!-- Same footer as index.html -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="src/js/data.js"></script>
    <script src="src/js/main.js"></script>
    <script>
      lucide.createIcons();

      // Favorites functionality
      document.addEventListener('DOMContentLoaded', function() {
        const favoritesBtn = document.getElementById('favoritesBtn');
        const favoritesPanel = document.getElementById('favoritesPanel');
        const closeFavorites = document.getElementById('closeFavorites');
        const favoritesList = document.getElementById('favoritesList');
        const favoritesCount = document.getElementById('favoritesCount');

        // Load favorites from localStorage
        async function loadFavorites() {
            try {
                const response = await fetch('src/api/favorites.php');
                const data = await response.json();
                
                if (!data.success) {
                    throw new Error(data.message || 'Failed to load favorites');
                }

                const favorites = data.favorites;
                favoritesCount.textContent = favorites.length;
                
                if (favorites.length === 0) {
                    favoritesList.innerHTML = '<div class="empty-favorites">No favorite books yet</div>';
                    return;
                }

                favoritesList.innerHTML = favorites
                    .slice(0, 5) // Show only last 5 favorites
                    .map(book => `
                        <div class="favorite-item" data-id="${book.id}">
                            <img src="${book.imageUrl}" alt="${book.title}">
                            <div class="favorite-item-details">
                                <h6>${book.title}</h6>
                                <p>${book.author}</p>
                                <button class="remove-favorite" onclick="removeFavorite('${book.id}')">
                                    Remove
                                </button>
                            </div>
                        </div>
                    `).join('');
                
                lucide.createIcons();
            } catch (error) {
                console.error('Error loading favorites:', error);
                favoritesList.innerHTML = '<div class="empty-favorites">Error loading favorites</div>';
            }
        }

        // Toggle favorites panel
        favoritesBtn.addEventListener('click', function(e) {
          e.preventDefault();
          favoritesPanel.classList.toggle('show');
          loadFavorites();
        });

        closeFavorites.addEventListener('click', function() {
          favoritesPanel.classList.remove('show');
        });

        // Remove favorite
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

                loadFavorites();
                showToast('Book removed from favorites');
            } catch (error) {
                console.error('Error removing favorite:', error);
                showToast('Error removing book from favorites');
            }
        };

        // Add favorite
        window.addFavorite = async function(book) {
            if (!book || !book.id || !book.title || !book.author || !book.imageUrl) {
                console.error('Invalid book data:', book);
                return;
            }

            try {
                const response = await fetch('src/api/favorites.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        bookId: book.id,
                        action: 'add',
                        bookData: {
                            title: book.title,
                            author: book.author,
                            imageUrl: book.imageUrl
                        }
                    })
                });

                const data = await response.json();
                
                if (!data.success) {
                    throw new Error(data.message || 'Failed to add favorite');
                }

                loadFavorites();
                showToast('Book added to favorites');
            } catch (error) {
                console.error('Error adding favorite:', error);
                showToast('Error adding book to favorites');
            }
        };

        // Toast notification function
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

        // Initial load
        loadFavorites();
      });
    </script>
  </body>
</html>
