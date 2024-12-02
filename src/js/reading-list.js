function updateReadingList() {
    const userId = 'user1'; // Temporary userId
    const readingList = document.getElementById('readingList');
    if (!readingList) return;

    const borrowedBooks = window.db.getBorrowedBooks(userId);
    const books = window.db.getAllBooks();

    if (!borrowedBooks || borrowedBooks.length === 0) {
        readingList.innerHTML = `
            <div class="text-center py-5">
                <i data-lucide="book-x" style="width: 48px; height: 48px; color: #8b7355;"></i>
                <h3 class="font-playfair mt-3">Your Reading List is Empty</h3>
                <p class="text-muted">Start adding books to your reading list!</p>
                <a href="index.html" class="btn vintage-btn mt-3">Browse Books</a>
            </div>
        `;
        lucide.createIcons();
        return;
    }

    readingList.innerHTML = borrowedBooks.map(borrowed => {
        const book = books.find(b => b.id === borrowed.bookId);
        if (!book) return '';

        const dueDate = new Date(borrowed.dueDate);
        const daysLeft = Math.ceil((dueDate - new Date()) / (1000 * 60 * 60 * 24));
        
        return `
            <div class="list-group-item">
                <div class="row align-items-center">
                    <div class="col-md-2">
                        <img src="${book.imageUrl}" alt="${book.title}" class="img-fluid rounded">
                    </div>
                    <div class="col-md-7">
                        <h5 class="font-playfair mb-1">${book.title}</h5>
                        <p class="text-muted mb-2">by ${book.author}</p>
                        <div class="d-flex align-items-center text-muted small">
                            <i data-lucide="calendar" class="me-2" style="width: 16px;"></i>
                            Due in ${daysLeft} days
                        </div>
                    </div>
                    <div class="col-md-3 text-md-end mt-3 mt-md-0">
                        <button class="btn vintage-btn" onclick="returnBook('${book.id}')">
                            <i data-lucide="book-marked" class="me-2"></i>
                            Return Book
                        </button>
                    </div>
                </div>
            </div>
        `;
    }).join('');

    lucide.createIcons();
}

function returnBook(bookId) {
    const userId = 'user1'; // Temporary userId
    window.db.returnBook(userId, bookId);
    updateReadingList();
    showToast('Book returned successfully!');
}

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

document.addEventListener('DOMContentLoaded', updateReadingList); 