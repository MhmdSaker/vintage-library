function filterByGenre(genre) {
    const books = window.db.getBooksByGenre(genre);
    displayFilteredBooks(books);
}

function displayFilteredBooks(books) {
    const booksContainer = document.getElementById('booksGrid');
    if (!booksContainer) return;

    if (books.length === 0) {
        booksContainer.innerHTML = `
            <div class="col-12 text-center">
                <p>No books found in this category.</p>
            </div>
        `;
        return;
    }

    booksContainer.innerHTML = books.map((book, index) => 
        createBookCard(book, index)
    ).join('');
    
    lucide.createIcons();
} 