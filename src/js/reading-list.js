async function updateReadingList() {
    const readingList = document.getElementById('readingList');
    if (!readingList) return;

    // Show loading state
    readingList.innerHTML = `
        <div class="text-center py-5">
            <div class="spinner-border text-secondary" role="status"></div>
            <p class="mt-2">Loading your reading list...</p>
        </div>
    `;

    try {
        // Fetch reading list from API
        const response = await fetch('src/api/reading-list.php');
        const data = await response.json();
        
        if (!data.success) {
            throw new Error(data.message || 'Failed to load reading list');
        }
        
        const books = data.books;
        
        if (!books || books.length === 0) {
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

        // Generate reading list items
        readingList.innerHTML = books.map(book => {
            // Calculate percentage for progress bar
            const percentComplete = book.total_pages > 0 
                ? Math.floor((book.current_page / book.total_pages) * 100) 
                : 0;
            
            const completedClass = book.completed ? 'completed' : '';
            const statusLabel = book.completed ? 'Completed' : 'In Progress';
            const statusClass = book.completed ? 'completed' : '';
            
            return `
                <div class="reading-list-item ${completedClass} p-4">
                    <div class="row align-items-center">
                        <div class="col-md-9">
                            <h5 class="font-playfair mb-1">${book.title}</h5>
                            <p class="text-muted mb-2">by ${book.author}</p>
                            
                            <div class="d-flex align-items-center mt-3">
                                <div class="book-progress flex-grow-1 me-3">
                                    <div class="progress-bar" role="progressbar" style="width: ${percentComplete}%" 
                                         aria-valuenow="${percentComplete}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <span class="book-status ${statusClass}">${statusLabel}</span>
                            </div>
                            
                            <div class="d-flex align-items-center mt-3">
                                <label class="me-2" for="page-${book.id}">Page:</label>
                                <input type="number" class="page-input me-2" value="${book.current_page}" 
                                       min="0" max="${book.total_pages}" 
                                       id="page-${book.id}" 
                                       ${book.completed ? 'disabled' : ''}>
                                <span class="text-muted">of ${book.total_pages}</span>
                                
                                <div class="ms-auto">
                                    <button class="btn btn-sm vintage-outline-btn" 
                                            onclick="updateProgress('${book.id}')" 
                                            ${book.completed ? 'disabled' : ''}>
                                        Update
                                    </button>
                                    
                                    <button class="btn btn-sm ${book.completed ? 'btn-outline-secondary' : 'vintage-btn'}" 
                                            onclick="markCompleted('${book.id}', ${!book.completed})">
                                        ${book.completed ? 'Mark as In Progress' : 'Mark as Completed'}
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 text-md-end mt-3 mt-md-0">
                            <div class="d-flex align-items-center justify-content-md-end">
                                <button class="btn vintage-outline-btn me-2" onclick="removeFromReadingList('${book.id}')">
                                    <i data-lucide="trash-2" class="me-1"></i>
                                    Remove
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }).join('');

        // Update the reading stats
        updateReadingStats(books);
        
        lucide.createIcons();
    } catch (error) {
        console.error('Error loading reading list:', error);
        readingList.innerHTML = `
            <div class="text-center py-5">
                <i data-lucide="alert-circle" style="width: 48px; height: 48px; color: #dc3545;"></i>
                <h3 class="font-playfair mt-3">Error Loading Reading List</h3>
                <p class="text-muted">Please try again later</p>
            </div>
        `;
        lucide.createIcons();
    }
}

// Update reading statistics
function updateReadingStats(books) {
    if (!books) return;
    
    const totalBooks = books.length;
    const completedBooks = books.filter(book => book.completed == 1 || book.completed === true).length;
    const inProgressBooks = totalBooks - completedBooks;
    
    const statsContainer = document.getElementById('readingStats');
    if (statsContainer) {
        statsContainer.innerHTML = `
            <div class="vintage-card mb-4">
                <h3 class="font-playfair h5 mb-4">Reading Stats</h3>
                <div class="row text-center g-3">
                    <div class="col-6">
                        <div class="stats-number">${totalBooks}</div>
                        <div class="stats-label">Total Books</div>
                    </div>
                    <div class="col-6">
                        <div class="stats-number">${completedBooks}</div>
                        <div class="stats-label">Completed</div>
                    </div>
                    <div class="col-12">
                        <div class="mt-3">
                            <div class="d-flex justify-content-between mb-2">
                                <small>Overall Progress</small>
                                <small id="overallProgress">${calculateOverallProgress(books)}%</small>
                            </div>
                            <div class="progress" style="height: 8px">
                                <div class="progress-bar" role="progressbar" 
                                     style="width: ${calculateOverallProgress(books)}%" 
                                     aria-valuenow="${calculateOverallProgress(books)}" 
                                     aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }
}

// Calculate overall reading progress
function calculateOverallProgress(books) {
    if (!books || books.length === 0) return 0;
    
    const totalProgress = books.reduce((acc, book) => {
        const progress = book.total_pages > 0 
            ? book.current_page / book.total_pages 
            : 0;
        return acc + progress;
    }, 0);
    
    return Math.round((totalProgress / books.length) * 100);
}

// Update reading progress
async function updateProgress(id) {
    const pageInput = document.getElementById(`page-${id}`);
    if (!pageInput) return;
    
    const currentPage = parseInt(pageInput.value);
    const maxPages = parseInt(pageInput.max);
    
    // Validate input
    if (isNaN(currentPage) || currentPage < 0 || currentPage > maxPages) {
        showToast('Please enter a valid page number');
        return;
    }
    
    try {
        // Determine if the book is now completed
        const completed = currentPage >= maxPages;
        
        const response = await fetch('src/api/reading-list.php', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                id: id,
                current_page: currentPage,
                completed: completed
            })
        });
        
        const data = await response.json();
        
        if (data.error) {
            showToast(data.error);
        } else {
            showToast('Reading progress updated!');
            updateReadingList(); // Refresh the list
        }
    } catch (error) {
        console.error('Error updating progress:', error);
        showToast('Error updating reading progress');
    }
}

// Mark book as completed or in progress
async function markCompleted(id, completed) {
    try {
        const response = await fetch('src/api/reading-list.php', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                id: id,
                completed: completed
            })
        });
        
        const data = await response.json();
        
        if (data.error) {
            showToast(data.error);
        } else {
            showToast(`Book marked as ${completed ? 'completed' : 'in progress'}!`);
            updateReadingList(); // Refresh the list
        }
    } catch (error) {
        console.error('Error marking book:', error);
        showToast('Error updating book status');
    }
}

// Remove book from reading list
async function removeFromReadingList(id) {
    if (!confirm('Are you sure you want to remove this book from your reading list?')) {
        return;
    }
    
    try {
        const response = await fetch(`src/api/reading-list.php?id=${id}`, {
            method: 'DELETE'
        });
        
        const data = await response.json();
        
        if (data.error) {
            showToast(data.error);
        } else {
            showToast('Book removed from reading list!');
            updateReadingList(); // Refresh the list
        }
    } catch (error) {
        console.error('Error removing book:', error);
        showToast('Error removing book from reading list');
    }
}

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

// Initialize when the page loads
document.addEventListener('DOMContentLoaded', function() {
    updateReadingList();
    
    // Also initialize favorites count if favorites panel exists
    updateFavoritesCount();
});

// Update favorites count in the navbar
async function updateFavoritesCount() {
    const favoritesCount = document.getElementById('favoritesCount');
    if (!favoritesCount) return;
    
    try {
        const response = await fetch('src/api/favorites.php');
        const data = await response.json();
        
        if (data.success && data.favorites) {
            favoritesCount.textContent = data.favorites.length;
        }
    } catch (error) {
        console.error('Error updating favorites count:', error);
    }
} 