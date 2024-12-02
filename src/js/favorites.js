document.addEventListener('DOMContentLoaded', () => {
    if (!currentUser) {
        window.location.href = 'index.html';
        return;
    }

    renderFavoriteBooks();
});

function renderFavoriteBooks() {
    const favoriteBooksContainer = document.getElementById('favoriteBooks');
    const favoriteBooks = window.booksData.filter(book => 
        currentUser.favoriteBooks.includes(book.id)
    );

    if (favoriteBooks.length === 0) {
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
                        <span class="badge bg-light text-dark">${book.genre}</span>
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
}

function removeFromFavorites(bookId) {
    const index = currentUser.favoriteBooks.indexOf(bookId);
    if (index > -1) {
        currentUser.favoriteBooks.splice(index, 1);
        // In a real app, you would save this to a backend
        renderFavoriteBooks();
        alert('Book removed from favorites!');
    }
} 