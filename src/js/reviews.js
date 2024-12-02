// Reviews Manager Class
class ReviewsManager {
    constructor() {
        this.reviews = JSON.parse(localStorage.getItem('bookReviews') || '[]');
        this.initializeUI();
        this.loadBooks();
        this.setupEventListeners();
    }

    // Initialize UI elements
    initializeUI() {
        this.bookSelect = document.querySelector('.reviews-filter select:first-child');
        this.sortSelect = document.querySelector('.reviews-filter select:last-child');
        this.reviewsList = document.getElementById('reviewsList');
        this.reviewForm = document.getElementById('reviewForm');
    }

    // Load books into the select dropdown
    loadBooks() {
        const books = window.db.getAllBooks();
        this.bookSelect.innerHTML = `
            <option value="">All Books</option>
            ${books.map(book => `
                <option value="${book.id}">${book.title} by ${book.author}</option>
            `).join('')}
        `;
    }

    // Setup event listeners
    setupEventListeners() {
        // Form submission
        this.reviewForm.addEventListener('submit', (e) => {
            e.preventDefault();
            this.addReview();
        });

        // Filtering and sorting
        this.bookSelect.addEventListener('change', () => this.displayReviews());
        this.sortSelect.addEventListener('change', () => this.displayReviews());
    }

    // Add a new review
    addReview() {
        const bookId = document.getElementById('bookSelect').value;
        const rating = document.querySelector('input[name="rating"]:checked')?.value;
        const reviewText = document.getElementById('reviewText').value;

        if (!bookId || !rating || !reviewText.trim()) {
            showToast('Please fill in all fields', 'warning');
            return;
        }

        const review = {
            id: Date.now(),
            bookId,
            rating: parseInt(rating),
            text: reviewText.trim(),
            date: new Date().toISOString(),
            userName: 'Anonymous User' // In a real app, this would come from user authentication
        };

        this.reviews.push(review);
        this.saveReviews();
        this.displayReviews();
        this.reviewForm.reset();
        showToast('Review added successfully!');
    }

    // Save reviews to localStorage
    saveReviews() {
        localStorage.setItem('bookReviews', JSON.stringify(this.reviews));
    }

    // Get book details by ID
    getBookDetails(bookId) {
        return window.db.getBookById(bookId);
    }

    // Format date for display
    formatDate(dateString) {
        const options = { year: 'numeric', month: 'long', day: 'numeric' };
        return new Date(dateString).toLocaleDateString(undefined, options);
    }

    // Generate star rating HTML
    generateStarRating(rating) {
        return '★'.repeat(rating) + '☆'.repeat(5 - rating);
    }

    // Sort reviews based on selected option
    sortReviews(reviews) {
        const sortOption = this.sortSelect.value;
        return [...reviews].sort((a, b) => {
            switch (sortOption) {
                case 'Highest Rated':
                    return b.rating - a.rating;
                case 'Lowest Rated':
                    return a.rating - b.rating;
                default: // Latest First
                    return new Date(b.date) - new Date(a.date);
            }
        });
    }

    // Display reviews
    displayReviews() {
        const selectedBookId = this.bookSelect.value;
        let filteredReviews = this.reviews;

        if (selectedBookId) {
            filteredReviews = this.reviews.filter(review => review.bookId === selectedBookId);
        }

        const sortedReviews = this.sortReviews(filteredReviews);

        this.reviewsList.innerHTML = sortedReviews.length ? sortedReviews.map(review => {
            const book = this.getBookDetails(review.bookId);
            return `
                <div class="review-card">
                    <div class="d-flex align-items-start gap-3 mb-3">
                        <div class="review-avatar">
                            <i data-lucide="user"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="mb-1 font-playfair">${book.title}</h5>
                            <div class="review-meta">
                                <span class="me-3">${review.userName}</span>
                                <span>${this.formatDate(review.date)}</span>
                            </div>
                        </div>
                        <div class="rating">${this.generateStarRating(review.rating)}</div>
                    </div>
                    <p class="review-text mb-0">${review.text}</p>
                </div>
            `;
        }).join('') : `
            <div class="text-center py-5">
                <i data-lucide="book-x" class="mb-3" style="width: 48px; height: 48px; color: #8b7355;"></i>
                <h5 class="font-playfair">No reviews yet</h5>
                <p class="text-muted">Be the first to share your thoughts!</p>
            </div>
        `;

        // Reinitialize icons for new content
        lucide.createIcons();
    }
}

// Create toast container if it doesn't exist
function createToastContainer() {
    if (!document.getElementById('toastContainer')) {
        const container = document.createElement('div');
        container.id = 'toastContainer';
        container.className = 'toast-container position-fixed bottom-0 end-0 p-3';
        container.style.zIndex = '1050';
        document.body.appendChild(container);
    }
}

// Show toast notification
function showToast(message, type = 'success') {
    const toastContainer = document.getElementById('toastContainer');
    const toastId = Date.now();
    
    const toast = document.createElement('div');
    toast.className = `toast align-items-center border-0 bg-${type}`;
    toast.setAttribute('role', 'alert');
    toast.setAttribute('aria-live', 'assertive');
    toast.setAttribute('aria-atomic', 'true');
    
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body text-white">
                ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;
    
    toastContainer.appendChild(toast);
    const bsToast = new bootstrap.Toast(toast);
    bsToast.show();
    
    toast.addEventListener('hidden.bs.toast', () => toast.remove());
}

// Initialize when page loads
document.addEventListener('DOMContentLoaded', () => {
    createToastContainer();
    window.reviewsManager = new ReviewsManager();
    window.reviewsManager.displayReviews();
});

// Reviews handling
document.addEventListener('DOMContentLoaded', function() {
    // Initialize the books dropdown
    populateBookSelect();
    // Load existing reviews
    loadReviews();
    
    // Handle review form submission
    document.getElementById('reviewForm').addEventListener('submit', handleReviewSubmit);
});

function populateBookSelect() {
    const bookSelect = document.getElementById('bookSelect');
    const books = JSON.parse(localStorage.getItem('books')) || [];
    
    books.forEach(book => {
        const option = document.createElement('option');
        option.value = book.id;
        option.textContent = book.title;
        bookSelect.appendChild(option);
    });
}

function handleReviewSubmit(e) {
    e.preventDefault();
    
    const bookId = document.getElementById('bookSelect').value;
    const rating = document.querySelector('input[name="rating"]:checked').value;
    const reviewText = document.getElementById('reviewText').value;
    
    // Get existing reviews or initialize empty array
    const reviews = JSON.parse(localStorage.getItem('reviews')) || [];
    
    // Create new review object
    const newReview = {
        id: Date.now(),
        bookId: bookId,
        rating: parseInt(rating),
        text: reviewText,
        date: new Date().toISOString(),
        userId: 'user123' // In a real app, this would come from auth
    };
    
    // Add new review
    reviews.push(newReview);
    
    // Save to localStorage
    localStorage.setItem('reviews', JSON.stringify(reviews));
    
    // Refresh reviews display
    loadReviews();
    
    // Reset form
    e.target.reset();
    
    // Show success message
    alert('Review submitted successfully!');
}

function loadReviews() {
    const reviewsList = document.getElementById('reviewsList');
    const reviews = JSON.parse(localStorage.getItem('reviews')) || [];
    const books = JSON.parse(localStorage.getItem('books')) || [];
    
    // Clear current reviews
    reviewsList.innerHTML = '';
    
    reviews.forEach(review => {
        const book = books.find(b => b.id === review.bookId);
        if (!book) return;
        
        const reviewElement = createReviewElement(review, book);
        reviewsList.appendChild(reviewElement);
    });
}

function createReviewElement(review, book) {
    const div = document.createElement('div');
    div.className = 'review-card';
    
    const stars = '★'.repeat(review.rating) + '☆'.repeat(5 - review.rating);
    const date = new Date(review.date).toLocaleDateString();
    
    div.innerHTML = `
        <div class="d-flex align-items-center mb-3">
            <div class="review-avatar me-3">
                <i data-lucide="user"></i>
            </div>
            <div>
                <h5 class="mb-1">${book.title}</h5>
                <div class="rating">${stars}</div>
            </div>
        </div>
        <p class="review-text mb-2">${review.text}</p>
        <div class="review-meta">
            Posted on ${date}
        </div>
    `;
    
    // Re-initialize Lucide icons
    lucide.createIcons({
        target: div
    });
    
    return div;
}
