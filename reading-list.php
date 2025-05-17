<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "mhmd090";
$dbname = "vintage_library";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle "Borrow" / Add to reading list action
$message = '';
if (isset($_GET['action']) && $_GET['action'] === 'add' && isset($_GET['book_id'])) {
    $book_id = (int)$_GET['book_id'];
    $return_url = isset($_GET['return_url']) ? $_GET['return_url'] : 'reading-list.php';
    
    // Default total_pages to 100 if not provided
    $total_pages = isset($_GET['total_pages']) ? (int)$_GET['total_pages'] : 100;
    
    // Check if the book is already in the reading list
    $check_sql = "SELECT id FROM reading_list WHERE book_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("i", $book_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows === 0) {
        // Book is not in reading list yet, add it
        $add_sql = "INSERT INTO reading_list (book_id, total_pages, current_page) VALUES (?, ?, 0)";
        $add_stmt = $conn->prepare($add_sql);
        $add_stmt->bind_param("ii", $book_id, $total_pages);
        
        if ($add_stmt->execute()) {
            // Reduce available copies by 1
            $update_copies = "UPDATE books SET copies_available = copies_available - 1 WHERE id = ? AND copies_available > 0";
            $update_stmt = $conn->prepare($update_copies);
            $update_stmt->bind_param("i", $book_id);
            $update_stmt->execute();
            
            $message = "Book added to your reading list successfully!";
        } else {
            $message = "Error adding book to reading list: " . $conn->error;
        }
    } else {
        $message = "This book is already in your reading list.";
    }
    
    // If we came from another page and want to go back there
    if ($return_url !== 'reading-list.php') {
        header("Location: $return_url");
        exit;
    }
}

// Fetch current reading list
$reading_list = [];
$sql = "SELECT r.*, b.title, b.author, b.image_url, b.genre 
        FROM reading_list r 
        JOIN books b ON r.book_id = b.id 
        ORDER BY r.completed ASC, r.date_added DESC";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $reading_list[] = $row;
    }
}

// Calculate reading statistics
$total_books = count($reading_list);
$completed_books = 0;
$total_pages_read = 0;
$total_pages_to_read = 0;

foreach ($reading_list as $book) {
    if ($book['completed']) {
        $completed_books++;
    }
    $total_pages_read += $book['current_page'];
    $total_pages_to_read += $book['total_pages'];
}

// Fetch recent favorites for the sidebar
$favorites = [];
$favorites_sql = "SELECT b.id, b.title, b.author, b.image_url FROM favorites f JOIN books b ON f.book_id = b.id ORDER BY f.date_added DESC LIMIT 5";
$favorites_result = $conn->query($favorites_sql);
if ($favorites_result && $favorites_result->num_rows > 0) {
    while ($row = $favorites_result->fetch_assoc()) {
        $favorites[] = $row;
    }
}
$total_favorites = count($favorites);
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Reading List - Vintage Library</title>
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
      .reading-list-item {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        margin-bottom: 1rem;
      }

      .reading-list-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(139, 115, 85, 0.15);
      }

      .reading-list-item.completed {
        background-color: #f8f9fa;
        border-left: 4px solid #8b7355;
      }

      .book-progress {
        height: 8px;
        background-color: #e9ecef;
        border-radius: 4px;
      }

      .book-progress .progress-bar {
        background-color: #8b7355;
        border-radius: 4px;
      }

      .book-status {
        font-size: 0.875rem;
        padding: 0.25rem 0.75rem;
        border-radius: 50px;
        background-color: rgba(139, 115, 85, 0.1);
        color: #8b7355;
      }

      .book-status.completed {
        background-color: rgba(40, 167, 69, 0.1);
        color: #28a745;
      }

      .page-input {
        width: 80px;
        border: 1px solid #ced4da;
        border-radius: 4px;
        padding: 0.375rem 0.75rem;
      }

      .vintage-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        padding: 1.5rem;
      }

      .stats-number {
        font-size: 2rem;
        font-family: "Playfair Display", serif;
        color: #8b7355;
      }

      .stats-label {
        font-size: 0.875rem;
        color: #6c757d;
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
        font-family: "Playfair Display", serif;
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

      /* Toast styling */
      .toast-container {
        position: fixed;
        bottom: 20px;
        right: 20px;
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
  <body class="d-flex flex-column min-vh-100">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark custom-nav">
      <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="index.php">
          <i data-lucide="library" class="me-2"></i>
          <span class="font-playfair">Vintage Library</span>
        </a>
        <button
          class="navbar-toggler"
          type="button"
          data-bs-toggle="collapse"
          data-bs-target="#navbarNav"
          aria-controls="navbarNav"
          aria-expanded="false"
          aria-label="Toggle navigation"
        >
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav me-auto">
            <li class="nav-item">
              <a
                class="nav-link px-2"
                href="index.php"
                data-bs-toggle="tooltip"
                data-bs-placement="bottom"
                title="Home"
              >
                <i data-lucide="home"></i>
              </a>
            </li>
            <li class="nav-item">
              <a
                class="nav-link px-2"
                href="search-results.php"
                data-bs-toggle="tooltip"
                data-bs-placement="bottom"
                title="Browse Books"
              >
                <i data-lucide="book-open"></i>
              </a>
            </li>
            <li class="nav-item">
              <a
                class="nav-link px-2"
                href="categories.php"
                data-bs-toggle="tooltip"
                data-bs-placement="bottom"
                title="Categories"
              >
                <i data-lucide="list"></i>
              </a>
            </li>
            <li class="nav-item favorites-container">
              <a
                class="nav-link px-2 position-relative"
                href="#"
                data-bs-toggle="tooltip"
                data-bs-placement="bottom"
                title="Favorites"
              >
                <i data-lucide="heart"></i>
                <span class="favorites-badge" id="favoritesCount"><?php echo $total_favorites; ?></span>
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
                        <a href="fix-favorites.php?action=remove&book_id=<?php echo $book['id']; ?>&return_url=reading-list.php" class="favorite-item-remove" title="Remove Favorite">
                          <i data-lucide="x"></i>
                        </a>
                      </div>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </div>
              </div>
            </li>
            <li class="nav-item">
              <a
                class="nav-link px-2 active"
                href="reading-list.php"
                data-bs-toggle="tooltip"
                data-bs-placement="bottom"
                title="Reading List"
              >
                <i data-lucide="list-checks"></i>
              </a>
            </li>
            <li class="nav-item">
              <a
                class="nav-link px-2"
                href="events.php"
                data-bs-toggle="tooltip"
                data-bs-placement="bottom"
                title="Events"
              >
                <i data-lucide="calendar"></i>
              </a>
            </li>
            <li class="nav-item">
              <a
                class="nav-link px-2"
                href="gallery.php"
                data-bs-toggle="tooltip"
                data-bs-placement="bottom"
                title="Gallery"
              >
                <i data-lucide="image"></i>
              </a>
            </li>
            <li class="nav-item">
              <a
                class="nav-link px-2"
                href="reviews.php"
                data-bs-toggle="tooltip"
                data-bs-placement="bottom"
                title="Reviews"
              >
                <i data-lucide="star"></i>
              </a>
            </li>
            <li class="nav-item">
              <a
                class="nav-link px-2"
                href="add-book.php"
                data-bs-toggle="tooltip"
                data-bs-placement="bottom"
                title="Add Book"
              >
                <i data-lucide="plus-circle"></i>
              </a>
            </li>
            <li class="nav-item">
              <a
                class="nav-link px-2"
                href="about.php"
                data-bs-toggle="tooltip"
                data-bs-placement="bottom"
                title="About"
              >
                <i data-lucide="info"></i>
              </a>
            </li>
            <li class="nav-item">
              <a
                class="nav-link px-2"
                href="contact.php"
                data-bs-toggle="tooltip"
                data-bs-placement="bottom"
                title="Contact"
              >
                <i data-lucide="mail"></i>
              </a>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <main class="container py-4">
      <div class="row mb-4">
        <div class="col-md-8">
          <h1 class="font-playfair mb-4">My Reading List</h1>
        </div>
        <div class="col-md-4 text-md-end">
          <button class="btn vintage-btn" data-bs-toggle="modal" data-bs-target="#addBookModal">
            Add Book<i data-lucide="plus" class="ms-2"></i>
          </button>
        </div>
      </div>

      <div class="row">
        <div class="col-md-8">
          <div id="readingList">
            <!-- Reading list items will be dynamically added here -->
          </div>
        </div>
        <div class="col-md-4">
          <div id="readingStats">
            <!-- Reading stats will be dynamically added here -->
          </div>
        </div>
      </div>
    </main>

    <!-- Add Book Modal -->
    <div class="modal fade" id="addBookModal" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title font-playfair">Add to Reading List</h5>
            <button
              type="button"
              class="btn-close"
              data-bs-dismiss="modal"
              title="Close"
            ></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label for="bookSelect" class="form-label">Select Book</label>
              <select class="form-select" id="bookSelect" title="Choose a book">
                <!-- Options will be populated dynamically -->
              </select>
            </div>
            <div class="mb-3">
              <label for="totalPages" class="form-label">Total Pages</label>
              <input type="number" class="form-control" id="totalPages" title="Enter total pages" placeholder="Number of pages" />
            </div>
          </div>
          <div class="modal-footer">
            <button
              type="button"
              class="btn btn-secondary"
              data-bs-dismiss="modal"
            >
              Cancel
            </button>
            <button
              type="button"
              class="btn vintage-btn"
              id="confirmAddBookBtn"
            >
              Add Book
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Toast container for notifications -->
    <div id="toastContainer" class="toast-container"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="src/js/reading-list.js"></script>
    <script>
      // Initialize tooltips and icons
      document.addEventListener("DOMContentLoaded", function() {
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        const tooltipList = [...tooltipTriggerList].map(
          (tooltipTriggerEl) => new bootstrap.Tooltip(tooltipTriggerEl)
        );
        lucide.createIcons();
        
        // Populate book select in modal
        populateBookSelect();
        
        // Add event listener for add book button
        document.getElementById('confirmAddBookBtn').addEventListener('click', addBookToReadingList);
      });
      
      // Populate book select with available books
      async function populateBookSelect() {
        try {
          const response = await fetch('src/api/books.php');
          const data = await response.json();
          
          if (data.success && data.books) {
            const selectElement = document.getElementById('bookSelect');
            selectElement.innerHTML = '<option value="">Select a book</option>';
            
            data.books.forEach(book => {
              const option = document.createElement('option');
              option.value = book.id;
              option.textContent = `${book.title} by ${book.author}`;
              selectElement.appendChild(option);
            });
          }
        } catch (error) {
          console.error('Error loading books:', error);
          showToast('Failed to load available books');
        }
      }
      
      // Add book to reading list
      async function addBookToReadingList() {
        const bookId = document.getElementById('bookSelect').value;
        const totalPages = document.getElementById('totalPages').value;
        
        if (!bookId) {
          showToast('Please select a book');
          return;
        }
        
        if (!totalPages || totalPages < 1) {
          showToast('Please enter a valid number of pages');
          return;
        }
        
        try {
          const response = await fetch('src/api/reading-list.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json'
            },
            body: JSON.stringify({
              book_id: bookId,
              total_pages: parseInt(totalPages),
              current_page: 0,
              completed: false
            })
          });
          
          const data = await response.json();
          
          if (data.error) {
            showToast(data.error);
          } else {
            // Close modal and refresh reading list
            const modal = bootstrap.Modal.getInstance(document.getElementById('addBookModal'));
            modal.hide();
            
            // Reset form
            document.getElementById('bookSelect').value = '';
            document.getElementById('totalPages').value = '';
            
            // Refresh reading list
            updateReadingList();
            showToast('Book added to reading list');
          }
        } catch (error) {
          console.error('Error adding book to reading list:', error);
          showToast('Failed to add book to reading list');
        }
      }
    </script>
  </body>
</html>
