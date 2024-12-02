// Function to load and display books
function loadBooks() {
    const booksContainer = document.getElementById('booksGrid');
    if (!booksContainer) return;

    const books = window.db.getAllBooks();
    
    if (!books || books.length === 0) {
        booksContainer.innerHTML = `
            <div class="col-12 text-center">
                <div class="no-results">
                    <i data-lucide="book-x" style="width: 48px; height: 48px;"></i>
                    <h3 class="font-playfair">No Books Available</h3>
                    <p class="text-muted">Check back later for new additions</p>
                </div>
            </div>
        `;
        return;
    }

    booksContainer.innerHTML = books.map(book => createBookCard(book)).join('');
    lucide.createIcons();
}

// Function to create a book card
function createBookCard(book) {
    return `
        <div class="col fade-in">
            <div class="book-card">
                <div class="book-card-inner">
                    <div class="book-card-front">
                        <img src="${book.imageUrl}" alt="${book.title}" class="card-img">
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
                            <p class="small mb-4">${book.description.substring(0, 150)}...</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <button class="btn btn-sm vintage-btn" onclick="borrowBook('${book.id}')">
                                    <i data-lucide="book-marked" class="me-1"></i>
                                    Borrow
                                </button>
                                <button class="btn btn-sm vintage-outline-btn" onclick="toggleFavorite('${book.id}')">
                                    <i data-lucide="heart" class="me-1"></i>
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

// Function to handle borrowing
function borrowBook(bookId) {
    const userId = 'user1'; // Temporary userId
    const success = window.db.borrowBook(userId, bookId);
    if (success) {
        showToast('Book added to your borrowed list!');
    } else {
        showToast('No copies available or already borrowed.');
    }
}

// Function to handle favorites
function toggleFavorite(bookId) {
    const userId = 'user1'; // Temporary userId
    window.db.toggleFavorite(userId, bookId);
    showToast('Favorites updated!');
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

// Initialize when the page loads
document.addEventListener('DOMContentLoaded', () => {
    loadBooks();
});

// Search functionality
const searchInput = document.getElementById('searchInput');
if (searchInput) {
    searchInput.addEventListener('input', (e) => {
        const searchTerm = e.target.value.toLowerCase();
        const books = JSON.parse(localStorage.getItem('books') || '[]');
        const filteredBooks = books.filter(book => 
            book.title.toLowerCase().includes(searchTerm) ||
            book.author.toLowerCase().includes(searchTerm) ||
            book.genre.toLowerCase().includes(searchTerm)
        );
        
        const booksContainer = document.getElementById('booksGrid');
        booksContainer.innerHTML = '';
        filteredBooks.forEach((book, index) => {
            const bookCard = createBookCard(book, index);
            booksContainer.appendChild(bookCard);
        });
        lucide.createIcons();
    });
} 