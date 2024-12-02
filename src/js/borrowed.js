document.addEventListener('DOMContentLoaded', () => {
    if (!currentUser) {
        window.location.href = 'index.html';
        return;
    }

    loadBorrowedBooks();
});

function loadBorrowedBooks() {
    const userId = 'user1'; // Temporary userId
    const borrowedBooks = window.db.getBorrowedBooks(userId);
    const books = window.db.getAllBooks();
    
    const borrowedBooksContainer = document.getElementById('borrowedBooks');
    
    if (!borrowedBooks || borrowedBooks.length === 0) {
        borrowedBooksContainer.innerHTML = `
            <div class="col-12 text-center py-5">
                <i data-lucide="book-x" style="width: 48px; height: 48px; color: #8b7355;"></i>
                <h3 class="font-playfair mt-3">No Borrowed Books</h3>
                <p class="text-muted">You haven't borrowed any books yet.</p>
                <a href="index.html" class="btn vintage-btn mt-3">Browse Books</a>
            </div>
        `;
        return;
    }

    borrowedBooksContainer.innerHTML = borrowedBooks.map(borrowed => {
        const book = books.find(b => b.id === borrowed.bookId);
        if (!book) return '';
        
        return `
            <div class="col-md-4">
                <div class="book-card">
                    <!-- Book card content -->
                    <button onclick="returnBook('${borrowed.bookId}')">Return Book</button>
                </div>
            </div>
        `;
    }).join('');
}

function returnBook(bookId) {
    const userId = 'user1'; // Temporary userId
    window.db.returnBook(userId, bookId);
    loadBorrowedBooks();
    showToast('Book returned successfully!');
} 