// Database module for API interactions
window.db = (function() {
    // API endpoints
    const API_URL = {
        BOOKS: 'src/api/books.php',
        READING_LIST: 'src/api/reading-list.php',
        REVIEWS: 'src/api/reviews.php'
    };

    // Error handler
    const handleError = (error) => {
        console.error('API Error:', error);
        showToast('Error: ' + (error.message || 'An unknown error occurred'));
        return null;
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

    // Books API
    const getAllBooks = async () => {
        try {
            const response = await fetch(API_URL.BOOKS);
            const data = await response.json();
            return data.books || [];
        } catch (error) {
            return handleError(error);
        }
    };

    const getBook = async (id) => {
        try {
            const response = await fetch(`${API_URL.BOOKS}?id=${id}`);
            const data = await response.json();
            return data;
        } catch (error) {
            return handleError(error);
        }
    };

    const addBook = async (bookData) => {
        try {
            const response = await fetch(API_URL.BOOKS, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(bookData)
            });
            const data = await response.json();
            return data;
        } catch (error) {
            return handleError(error);
        }
    };

    const updateBook = async (bookData) => {
        try {
            const response = await fetch(API_URL.BOOKS, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(bookData)
            });
            const data = await response.json();
            return data;
        } catch (error) {
            return handleError(error);
        }
    };

    const deleteBook = async (id) => {
        try {
            const response = await fetch(`${API_URL.BOOKS}?id=${id}`, {
                method: 'DELETE'
            });
            const data = await response.json();
            return data;
        } catch (error) {
            return handleError(error);
        }
    };

    const toggleFavorite = async (id) => {
        try {
            // First, get the current book data
            const book = await getBook(id);
            if (!book) return false;
            
            // Toggle the favorite status
            book.isFavorite = !book.isFavorite;
            
            // Update the book
            const result = await updateBook(book);
            return result && !result.error;
        } catch (error) {
            return handleError(error);
        }
    };

    // Reading List API
    const getReadingList = async () => {
        try {
            const response = await fetch(API_URL.READING_LIST);
            const data = await response.json();
            return data.books || [];
        } catch (error) {
            return handleError(error);
        }
    };

    const addToReadingList = async (bookId, totalPages = 1) => {
        try {
            const response = await fetch(API_URL.READING_LIST, {
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
            return data;
        } catch (error) {
            return handleError(error);
        }
    };

    const updateReadingProgress = async (id, currentPage, totalPages, completed = false) => {
        try {
            const response = await fetch(API_URL.READING_LIST, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    id: id,
                    current_page: currentPage,
                    total_pages: totalPages,
                    completed: completed
                })
            });
            const data = await response.json();
            return data;
        } catch (error) {
            return handleError(error);
        }
    };

    const removeFromReadingList = async (id) => {
        try {
            const response = await fetch(`${API_URL.READING_LIST}?id=${id}`, {
                method: 'DELETE'
            });
            const data = await response.json();
            return data;
        } catch (error) {
            return handleError(error);
        }
    };

    // Reviews API
    const getBookReviews = async (bookId) => {
        try {
            const response = await fetch(`${API_URL.REVIEWS}?book_id=${bookId}`);
            const data = await response.json();
            return data.reviews || [];
        } catch (error) {
            return handleError(error);
        }
    };

    const getAllReviews = async () => {
        try {
            const response = await fetch(API_URL.REVIEWS);
            const data = await response.json();
            return data.reviews || [];
        } catch (error) {
            return handleError(error);
        }
    };

    const addReview = async (bookId, reviewerName, rating, comment = '') => {
        try {
            const response = await fetch(API_URL.REVIEWS, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    book_id: bookId,
                    reviewer_name: reviewerName,
                    rating: rating,
                    comment: comment
                })
            });
            const data = await response.json();
            return data;
        } catch (error) {
            return handleError(error);
        }
    };

    const updateReview = async (id, reviewData) => {
        try {
            reviewData.id = id;
            const response = await fetch(API_URL.REVIEWS, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(reviewData)
            });
            const data = await response.json();
            return data;
        } catch (error) {
            return handleError(error);
        }
    };

    const deleteReview = async (id) => {
        try {
            const response = await fetch(`${API_URL.REVIEWS}?id=${id}`, {
                method: 'DELETE'
            });
            const data = await response.json();
            return data;
        } catch (error) {
            return handleError(error);
        }
    };

    // Utility functions
    const searchBooks = async (query) => {
        try {
            const books = await getAllBooks();
            if (!books) return [];
            
            query = query.toLowerCase();
            return books.filter(book => 
                book.title.toLowerCase().includes(query) ||
                book.author.toLowerCase().includes(query) ||
                book.genre.toLowerCase().includes(query) ||
                (book.description && book.description.toLowerCase().includes(query))
            );
        } catch (error) {
            return handleError(error);
        }
    };

    // Public API
    return {
        // Books
        getAllBooks,
        getBook,
        addBook,
        updateBook,
        deleteBook,
        toggleFavorite,
        
        // Reading List
        getReadingList,
        addToReadingList,
        updateReadingProgress,
        removeFromReadingList,
        
        // Reviews
        getBookReviews,
        getAllReviews,
        addReview,
        updateReview,
        deleteReview,
        
        // Utility
        searchBooks
    };
})(); 