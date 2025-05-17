document.addEventListener('DOMContentLoaded', () => {
    if (!currentUser) {
        window.location.href = 'index.html';
        return;
    }

    renderFavoriteBooks();
});

async function renderFavoriteBooks() {
    const favoriteBooksContainer = document.getElementById('favoriteBooks');
    if (!favoriteBooksContainer) return;
    
    try {
        // Show loading state
        favoriteBooksContainer.innerHTML = '<div class="col-12 text-center"><div class="spinner-border text-secondary" role="status"></div></div>';
        
        // Fetch favorites from the API
        const response = await fetch('src/api/favorites.php');
        const data = await response.json();
        
        if (!data.success) {
            throw new Error(data.message || 'Failed to load favorites');
        }
        
        const favoriteBooks = data.favorites;
        
        if (!favoriteBooks || favoriteBooks.length === 0) {
            favoriteBooksContainer.innerHTML = `
                <div class="col-12 text-center py-5">
                    <h3 class="font-playfair mb-3">No Favorite Books</h3>
                    <p class="text-muted mb-4">You haven't added any books to your favorites yet.</p>
                    <a href="index.html" class="btn vintage-btn">
                        <i data-lucide="book-open" class="me-2"></i>
                        Browse Books
                    </a>
                </div>
            `;
        } else {
            favoriteBooksContainer.innerHTML = favoriteBooks.map((book, index) => `
                <div class="col-md-6 col-lg-4 fade-in" style="animation-delay: ${index * 100}ms">
                    <div class="book-card p-3">
                        <div class="position-relative mb-3">
                            <img src="${book.imageUrl}" 
                                 alt="${book.title}" 
                                 class="book-image w-100">
                            <div class="book-badge">
                                <i data-lucide="heart" class="me-1"></i>
                                Favorite
                            </div>
                        </div>
                        <h3 class="book-title font-playfair h5 mb-2">${book.title}</h3>
                        <p class="book-author text-muted mb-3">by ${book.author}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="badge bg-light text-dark">${book.genre || 'Fiction'}</span>
                            <button class="btn vintage-btn" onclick="removeFromFavorites('${book.id}')">
                                <i data-lucide="heart-off" class="me-1"></i>
                                Remove
                            </button>
                        </div>
                    </div>
                </div>
            `).join('');
        }
        
        lucide.createIcons();
    } catch (error) {
        console.error('Error loading favorites:', error);
        favoriteBooksContainer.innerHTML = `
            <div class="col-12 text-center">
                <div class="alert alert-danger" role="alert">
                    Failed to load favorites. Please try again later.
                </div>
            </div>
        `;
    }
}

async function removeFromFavorites(bookId) {
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
        
        if (data.success) {
            // Show success message
            const toastContainer = document.getElementById('toastContainer') || createToastContainer();
            const toast = document.createElement('div');
            toast.className = 'toast show';
            toast.innerHTML = `<div class="toast-body">Book removed from favorites!</div>`;
            toastContainer.appendChild(toast);
            setTimeout(() => toast.remove(), 3000);
            
            // Refresh the favorites list
            renderFavoriteBooks();
        } else {
            throw new Error(data.message || 'Failed to remove from favorites');
        }
    } catch (error) {
        console.error('Error removing favorite:', error);
        alert('Error removing book from favorites. Please try again.');
    }
}

// Helper function to create toast container
function createToastContainer() {
    const container = document.createElement('div');
    container.id = 'toastContainer';
    container.className = 'toast-container position-fixed bottom-0 end-0 p-3';
    document.body.appendChild(container);
    return container;
} 