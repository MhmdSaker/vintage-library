// Initialize Lucide icons

document.addEventListener('DOMContentLoaded', function() {
    console.log('Main.js loaded');
    lucide.createIcons();

    // Initialize tooltips and popovers
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    console.log(tooltipTriggerList)
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });





    // Verify books data is loaded
    console.log('Books data:', window.booksData);






    // Set active nav item based on current page
    const currentPage = window.location.pathname.split('/').pop() || 'index.html';
    console.log(window.location.pathname)
    const navLinks = document.querySelectorAll('.nav-link');
    
    navLinks.forEach(link => {
        if (link.getAttribute('href') === currentPage) {
            link.classList.add('active');
        }
    });






    // Add animation to search box on focus
    const searchInput = document.querySelector('.search-input');
    if (searchInput) {
        searchInput.addEventListener('focus', () => {
            searchInput.closest('.search-box').classList.add('focused');
        });
        searchInput.addEventListener('blur', () => {
            searchInput.closest('.search-box').classList.remove('focused');
        });
    }

    // Load books if we're on the homepage
    if (window.location.pathname.endsWith('index.html') || window.location.pathname.endsWith('/')) {
        renderBooks();
    }
});





// Search functionality
const searchInput = document.getElementById('searchInput');
if (searchInput) {
    searchInput.addEventListener('input', (e) => {
        const query = e.target.value.toLowerCase();
        filterBooks(query);
    });
}

function filterBooks(query) {
    const books = document.querySelectorAll('.book-card');
    books.forEach(book => {
        const title = book.querySelector('.book-title').textContent.toLowerCase();
        const author = book.querySelector('.book-author').textContent.toLowerCase();
        
        if (title.includes(query) || author.includes(query)) {
            book.parentElement.style.display = 'block';
        } else {
            book.parentElement.style.display = 'none';
        }
    });
}

function sortBooks(criteria) {
    const booksGrid = document.getElementById('booksGrid');
    const books = Array.from(booksGrid.children);

    books.sort((a, b) => {
        const titleA = a.querySelector('.book-title').textContent;
        const titleB = b.querySelector('.book-title').textContent;
        const authorA = a.querySelector('.book-author').textContent;
        const authorB = b.querySelector('.book-author').textContent;

        switch(criteria) {
            case 'title':
                return titleA.localeCompare(titleB);
            case 'author':
                return authorA.localeCompare(authorB);
            case 'date':
                // Add date sorting logic if you have dates
                return 0;
            default:
                return 0;
        }
    });

    books.forEach(book => booksGrid.appendChild(book));
} 