function getBookIdFromUrl() {
    const params = new URLSearchParams(window.location.search);
    return params.get('id');
}

function renderBookDetails() {
    const bookId = getBookIdFromUrl();
    const book = window.booksData.find(b => b.id === bookId);
    
    if (!book) {
        document.getElementById('bookDetails').innerHTML = `
            <div class="text-center py-5">
                <h2 class="font-playfair mb-4">Book not found</h2>
                <a href="index.html" class="btn vintage-btn">
                    <i data-lucide="arrow-left" class="me-2"></i>
                    Return to Library
                </a>
            </div>
        `;
        return;
    }

    document.getElementById('bookDetails').innerHTML = `
        <a href="index.html" class="btn vintage-btn mb-4">
            <i data-lucide="arrow-left" class="me-2"></i>
            Back to Library
        </a>
        
        <div class="card vintage-card">
            <div class="card-body p-4">
                <div class="row">
                    <div class="col-md-4">
                        <div class="position-relative mb-4">
                            <img src="${book.imageUrl}" 
                                 alt="${book.title}" 
                                 class="book-image w-100">
                            <div class="book-badge">
                                <i data-lucide="book-marked" class="me-1"></i>
                                ${book.copiesAvailable} copies available
                            </div>
                        </div>
                        
                        <div class="card bg-light mb-4">
                            <div class="card-body">
                                <h5 class="font-playfair mb-3">Book Details</h5>
                                <ul class="list-unstyled mb-0">
                                    <li class="mb-2">
                                        <i data-lucide="book-copy" class="me-2"></i>
                                        ${book.pages} pages
                                    </li>
                                    <li class="mb-2">
                                        <i data-lucide="calendar" class="me-2"></i>
                                        ${book.publicationDate}
                                    </li>
                                    <li class="mb-2">
                                        <i data-lucide="globe-2" class="me-2"></i>
                                        ${book.language}
                                    </li>
                                    <li>
                                        <i data-lucide="building-2" class="me-2"></i>
                                        ${book.publisher}
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-8">
                        <h1 class="font-playfair mb-2">${book.title}</h1>
                        <p class="text-muted h5 mb-4">by ${book.author}</p>
                        
                        <div class="card bg-light mb-4">
                            <div class="card-body">
                                <h5 class="font-playfair mb-3">Description</h5>
                                <p class="mb-0">${book.description}</p>
                            </div>
                        </div>
                        
                        <button class="btn vintage-btn w-100 mb-3" onclick="borrowBook('${book.id}')">
                            <i data-lucide="book-marked" class="me-2"></i>
                            Borrow Book
                        </button>
                        
                        <p class="text-center text-muted">
                            <i data-lucide="clock" class="me-1"></i>
                            Standard loan period: 14 days
                        </p>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    lucide.createIcons();
}

function borrowBook(bookId) {
    if (!currentUser) {
        openAuthModal();
        return;
    }
    
    // Simulate borrowing functionality
    currentUser.borrowedBooks.push(bookId);
    alert('Book borrowed successfully!');
}

document.addEventListener('DOMContentLoaded', renderBookDetails); 