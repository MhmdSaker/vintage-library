// Reviews Manager Class
class ReviewsManager {
    constructor() {
        // Initialize empty state
        this.reviews = [];
        this.books = [];
        
        // Initialize UI elements
        this.initializeUI();
        
        // Setup event listeners
        this.setupEventListeners();
        
        // Load data
        this.initialize();
    }

    // Initialize the reviews manager
    async initialize() {
        try {
            // Load reviews from localStorage
            this.loadReviews();
            
            // Load books from JSON file
            this.books = await this.loadBooksFromJSON();
            
            // Populate UI
            this.populateBookSelects();
            this.displayReviews();
        } catch (error) {
            console.error('Initialization error:', error);
            showToast('Error initializing reviews', 'error');
        }
    }

    // Initialize UI elements
    initializeUI() {
        // Get filter elements
        this.filterSelect = document.querySelector('.reviews-filter select:first-child');
        this.sortSelect = document.querySelector('.reviews-filter select:last-child');
        
        // Get review form elements
        this.reviewForm = document.getElementById('reviewForm');
        this.bookSelect = document.getElementById('bookSelect');
        this.ratingInputs = document.querySelectorAll('input[name="rating"]');
        this.reviewText = document.getElementById('reviewText');
        
        // Get reviews list container
        this.reviewsList = document.getElementById('reviewsList');
    }

    // Setup event listeners
    setupEventListeners() {
        // Review form submission
        this.reviewForm.addEventListener('submit', (e) => {
            e.preventDefault();
            this.handleReviewSubmission();
        });

        // Filtering and sorting
        this.filterSelect.addEventListener('change', () => this.displayReviews());
        this.sortSelect.addEventListener('change', () => this.displayReviews());
    }

    // Load reviews from localStorage
    loadReviews() {
        try {
            this.reviews = JSON.parse(localStorage.getItem('bookReviews')) || [];
        } catch (error) {
            console.error('Error loading reviews:', error);
            this.reviews = [];
        }
    }

    // Save reviews to localStorage
    saveReviews() {
        try {
            localStorage.setItem('bookReviews', JSON.stringify(this.reviews));
        } catch (error) {
            console.error('Error saving reviews:', error);
            showToast('Error saving review', 'error');
        }
    }

    // Load books from JSON file
    async loadBooksFromJSON() {
        try {
            const response = await fetch('src/data/books.json');
            if (!response.ok) {
                throw new Error('Failed to load books');
            }
            const data = await response.json();
            return data.books || [];
        } catch (error) {
            console.error('Error loading books:', error);
            showToast('Error loading books data', 'error');
            return [];
        }
    }

    // Populate book select dropdowns
    populateBookSelects() {
        if (!this.books.length) {
            showToast('No books available', 'warning');
            return;
        }

        // Create options HTML for filter select
        const filterOptionsHTML = `
            <option value="">All Books</option>
            ${this.books.map(book => `
                <option value="${book.id}">${book.title} by ${book.author}</option>
            `).join('')}
        `;

        // Create options HTML for review form select
        const formOptionsHTML = `
            <option value="">Choose a book...</option>
            ${this.books.map(book => `
                <option value="${book.id}">${book.title} by ${book.author}</option>
            `).join('')}
        `;

        // Update select elements
        this.filterSelect.innerHTML = filterOptionsHTML;
        this.bookSelect.innerHTML = formOptionsHTML;
    }

    // Handle review submission
    handleReviewSubmission() {
        // Get form values
        const bookId = this.bookSelect.value;
        const rating = document.querySelector('input[name="rating"]:checked')?.value;
        const reviewText = this.reviewText.value.trim();

        // Validate form
        if (!bookId || !rating || !reviewText) {
            showToast('Please fill in all fields', 'warning');
            return;
        }

        // Create review object
        const review = {
            id: Date.now().toString(),
            bookId,
            rating: parseInt(rating),
            text: reviewText,
            date: new Date().toISOString(),
            userName: 'Anonymous User' // In a real app, this would come from user authentication
        };

        // Add review
        this.reviews.push(review);
        this.saveReviews();
        this.displayReviews();

        // Reset form
        this.reviewForm.reset();
        showToast('Review added successfully!', 'success');
    }

    // Get book details by ID
    getBookDetails(bookId) {
        const book = this.books.find(book => book.id === bookId);
        return book || {
            title: 'Unknown Book',
            author: 'Unknown Author'
        };
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
        const selectedBookId = this.filterSelect.value;
        let filteredReviews = this.reviews;

        // Filter by book if selected
        if (selectedBookId) {
            filteredReviews = this.reviews.filter(review => review.bookId === selectedBookId);
        }

        // Sort reviews
        const sortedReviews = this.sortReviews(filteredReviews);

        // Generate HTML
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

        // Reinitialize icons
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
});
