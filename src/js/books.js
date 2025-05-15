// Function to load and display books
async function loadBooks() {
    const booksContainer = document.getElementById('booksGrid');
    if (!booksContainer) return;

    // Show loading state
    booksContainer.innerHTML = '<div class="col-12 text-center"><div class="spinner-border text-secondary" role="status"></div></div>';

    try {
        // Fetch books from the API
        const response = await fetch('src/api/books.php');
        const data = await response.json();
        
        if (!data.success || !data.books || data.books.length === 0) {
            booksContainer.innerHTML = `
                <div class="col-12 text-center">
                    <div class="no-results">
                        <i data-lucide="book-x" style="width: 48px; height: 48px;"></i>
                        <h3 class="font-playfair">No Books Available</h3>
                        <p class="text-muted">Check back later for new additions</p>
                    </div>
                </div>
            `;
            lucide.createIcons();
            return;
        }

        booksContainer.innerHTML = data.books.map(book => createBookCard(book)).join('');
        lucide.createIcons();
    } catch (error) {
        console.error('Error loading books:', error);
        booksContainer.innerHTML = `
            <div class="col-12 text-center">
                <div class="no-results">
                    <i data-lucide="alert-circle" style="width: 48px; height: 48px;"></i>
                    <h3 class="font-playfair">Error Loading Books</h3>
                    <p class="text-muted">Please try again later</p>
                </div>
            </div>
        `;
        lucide.createIcons();
    }
}

// Function to create a book card
function createBookCard(book) {
    const heartIcon = book.is_favorite ? 'heart' : 'heart-outline';
    
    return `
        <div class="col fade-in">
            <div class="book-card">
                <div class="book-card-inner">
                    <div class="book-card-front">
                        <img src="${book.image_url}" alt="${book.title}" class="card-img">
                        <div class="card-img-overlay d-flex align-items-end">
                            <div class="book-card-details w-100 text-white p-3">
                                <h5 class="card-title font-playfair mb-0">${book.title}</h5>
                                <p class="card-text mb-0">${book.author}</p>
                            </div>
                        </div>
                    </div>
                    <div class="book-card-back">
                        <div class="p-3">
                            <h5 class="font-playfair">${book.title}</h5>
                            <p class="small text-muted mb-2">${book.author}</p>
                            <p class="small mb-4">${book.description ? book.description.substring(0, 150) + '...' : 'No description available.'}</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <button class="btn btn-sm vintage-btn" onclick="borrowBook('${book.id}')">
                                    <i data-lucide="book-copy" class="me-1"></i>
                                    Borrow
                                </button>
                                <button class="btn btn-sm vintage-outline-btn" onclick="addToReadingList('${book.id}')">
                                    <i data-lucide="book-marked" class="me-1"></i>
                                    Read
                                </button>
                                <button class="btn btn-sm vintage-outline-btn favorite-btn" onclick="toggleFavorite('${book.id}')">
                                    <i data-lucide="${heartIcon}" class="me-1"></i>
                                    Favorite
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
}

// Function to borrow a book (add to reading list)
async function borrowBook(bookId) {
    try {
        // Get book details first to get page count
        const bookResponse = await fetch(`src/api/books.php?id=${bookId}`);
        const bookData = await bookResponse.json();
        
        if (!bookData.success) {
            throw new Error(bookData.error || 'Failed to fetch book details');
        }
        
        // Default to 100 pages if not specified
        const totalPages = 100;
        
        // Add to reading list
        const response = await fetch('src/api/reading-list.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                book_id: bookId,
                total_pages: totalPages,
                current_page: 0,
                completed: false
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showToast('Book added to your reading list!');
            
            // Redirect to reading list page
            setTimeout(() => {
                window.location.href = 'reading-list.html';
            }, 1500);
        } else {
            showToast(data.error || 'Failed to add book to reading list.');
        }
    } catch (error) {
        console.error('Error borrowing book:', error);
        showToast('Error adding book to reading list.');
    }
}

// Function to add a book to reading list
async function addToReadingList(bookId) {
    try {
        // Open modal for adding to reading list
        const modal = new bootstrap.Modal(document.getElementById('addBookModal'));
        
        // Set the selected book ID in the modal
        document.getElementById('bookId').value = bookId;
        
        // Show the modal
        modal.show();
    } catch (error) {
        console.error('Error preparing reading list addition:', error);
        showToast('Error preparing to add book to reading list.');
    }
}

// Function to handle favorites
async function toggleFavorite(bookId) {
    try {
        const response = await fetch('src/api/favorites.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                bookId: bookId,
                action: 'toggle'
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showToast('Favorites updated!');
            // Reload books to show updated favorite status
            loadBooks();
        } else {
            showToast(data.error || 'Failed to update favorites.');
        }
    } catch (error) {
        console.error('Error toggling favorite:', error);
        showToast('Error updating favorites.');
    }
}

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

// Function to confirm adding to reading list from modal
function confirmAddToReadingList() {
    const bookId = document.getElementById('bookId').value;
    const totalPages = parseInt(document.getElementById('modalTotalPages').value);
    
    if (!bookId) {
        showToast('Book information is missing.');
        return;
    }
    
    if (isNaN(totalPages) || totalPages < 1) {
        showToast('Please enter a valid number of pages.');
        return;
    }
    
    // Add to reading list
    addBookToReadingList(bookId, totalPages);
    
    // Close the modal
    const modal = bootstrap.Modal.getInstance(document.getElementById('addBookModal'));
    modal.hide();
}

// Function to add a book to reading list with specified page count
async function addBookToReadingList(bookId, totalPages) {
    try {
        const response = await fetch('src/api/reading-list.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                book_id: bookId,
                total_pages: totalPages,
                current_page: 0,
                completed: false
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showToast('Book added to your reading list!');
        } else {
            showToast(data.error || 'Failed to add book to reading list.');
        }
    } catch (error) {
        console.error('Error adding to reading list:', error);
        showToast('Error adding book to reading list.');
    }
}

// Initialize when the page loads
document.addEventListener('DOMContentLoaded', () => {
    loadBooks();
});

// Search functionality
const searchInput = document.getElementById('searchInput');
if (searchInput) {
    searchInput.addEventListener('input', async (e) => {
        const searchTerm = e.target.value.toLowerCase();
        if (searchTerm.length === 0) {
            // If search is cleared, reload all books
            loadBooks();
            return;
        }
        
        const booksContainer = document.getElementById('booksGrid');
        
        try {
            // Show loading indicator
            booksContainer.innerHTML = '<div class="col-12 text-center"><div class="spinner-border text-secondary" role="status"></div></div>';
            
            // Fetch books and filter on client side (replace with server-side search API when available)
            const response = await fetch('src/api/books.php');
            const data = await response.json();
            
            if (!data.success || !data.books) {
                throw new Error('Failed to fetch books for search');
            }
            
            const filteredBooks = data.books.filter(book => 
                book.title.toLowerCase().includes(searchTerm) || 
                book.author.toLowerCase().includes(searchTerm) ||
                (book.description && book.description.toLowerCase().includes(searchTerm))
            );
            
            if (filteredBooks.length === 0) {
                booksContainer.innerHTML = `
                    <div class="col-12 text-center">
                        <div class="no-results">
                            <i data-lucide="search-x" style="width: 48px; height: 48px;"></i>
                            <h3 class="font-playfair">No Results Found</h3>
                            <p class="text-muted">Try a different search term</p>
                        </div>
                    </div>
                `;
            } else {
                booksContainer.innerHTML = filteredBooks.map(book => createBookCard(book)).join('');
            }
            
            lucide.createIcons();
        } catch (error) {
            console.error('Search error:', error);
            booksContainer.innerHTML = `
                <div class="col-12 text-center">
                    <div class="no-results">
                        <i data-lucide="alert-circle" style="width: 48px; height: 48px;"></i>
                        <h3 class="font-playfair">Error Searching</h3>
                        <p class="text-muted">Please try again</p>
                    </div>
                </div>
            `;
            lucide.createIcons();
        }
    });
} 