document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('addBookForm');
    const demoFillBtn = document.getElementById('demoFillBtn');
    const demoPresetSelect = document.getElementById('demoPresetSelect');

    if (demoFillBtn) {
        demoFillBtn.addEventListener('click', function() {
            const demoBooks = {
                'pride-prejudice': {
                    title: 'Pride and Prejudice',
                    author: 'Jane Austen',
                    isbn: '9780141439518',
                    genre: 'Classic Romance',
                    description: 'A sharp and enduring story of love, class, and personal growth in Regency England.',
                    imageUrl: 'https://images.pexels.com/photos/1907785/pexels-photo-1907785.jpeg?auto=compress&cs=tinysrgb&w=600',
                    copiesAvailable: '3',
                    publicationDate: '1813'
                },
                'dracula': {
                    title: 'Dracula',
                    author: 'Bram Stoker',
                    isbn: '9780141439846',
                    genre: 'Gothic Horror',
                    description: 'A chilling epistolary novel that shaped modern vampire fiction and Gothic suspense.',
                    imageUrl: 'https://images.pexels.com/photos/1765033/pexels-photo-1765033.jpeg?auto=compress&cs=tinysrgb&w=600',
                    copiesAvailable: '2',
                    publicationDate: '1897'
                },
                'sherlock': {
                    title: 'The Hound of the Baskervilles',
                    author: 'Arthur Conan Doyle',
                    isbn: '9780141034324',
                    genre: 'Mystery',
                    description: 'Sherlock Holmes investigates a legendary hound haunting the moors of Devonshire.',
                    imageUrl: 'https://images.pexels.com/photos/374716/pexels-photo-374716.jpeg?auto=compress&cs=tinysrgb&w=600',
                    copiesAvailable: '4',
                    publicationDate: '1902'
                },
                'little-prince': {
                    title: 'The Little Prince',
                    author: 'Antoine de Saint-Exupery',
                    isbn: '9780156012195',
                    genre: "Children's Literature",
                    description: 'A poetic tale about friendship, wonder, and seeing with the heart.',
                    imageUrl: 'https://images.pexels.com/photos/159711/books-bookstore-book-reading-159711.jpeg?auto=compress&cs=tinysrgb&w=600',
                    copiesAvailable: '5',
                    publicationDate: '1943'
                }
            };
            const selectedPreset = demoPresetSelect ? demoPresetSelect.value : 'pride-prejudice';
            const demoBook = demoBooks[selectedPreset] || demoBooks['pride-prejudice'];

            document.getElementById('title').value = demoBook.title;
            document.getElementById('author').value = demoBook.author;
            document.getElementById('isbn').value = demoBook.isbn;
            document.getElementById('genre').value = demoBook.genre;
            document.getElementById('description').value = demoBook.description;
            document.getElementById('imageUrl').value = demoBook.imageUrl;
            document.getElementById('copiesAvailable').value = demoBook.copiesAvailable;
            document.getElementById('publicationDate').value = demoBook.publicationDate;

            // Force floating labels to refresh immediately after programmatic fill.
            form.querySelectorAll('.form-control, .form-select').forEach((el) => {
                el.dispatchEvent(new Event('input', { bubbles: true }));
                el.dispatchEvent(new Event('change', { bubbles: true }));
            });

            showToast('Demo preset filled');
        });
    }

    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        if (this.checkValidity()) {
            const formData = new FormData(form);
            const newBook = {
                title: formData.get('title'),
                author: formData.get('author'),
                isbn: formData.get('isbn'),
                genre: formData.get('genre'),
                description: formData.get('description'),
                publicationDate: formData.get('publicationDate') || new Date().getFullYear().toString(),
                copiesAvailable: parseInt(formData.get('copiesAvailable')) || 1,
                imageUrl: formData.get('imageUrl') || 'https://images.pexels.com/photos/1907785/pexels-photo-1907785.jpeg?auto=compress&cs=tinysrgb&w=600',
                language: formData.get('language') || 'English',
                format: formData.get('format') || 'Hardcover',
                condition: formData.get('condition') || 'Good'
            };

            try {
                // Send the book data to the API
                const response = await fetch('src/api/add-book.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(newBook)
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Show success message
                    showToast('Book added successfully!');
                    
                    // Reset form
                    form.reset();
                    
                    // Redirect to homepage after short delay
                    setTimeout(() => {
                        window.location.href = 'index.php';
                    }, 2000);
                } else {
                    throw new Error(data.message || 'Failed to add book');
                }
            } catch (error) {
                console.error('Error adding book:', error);
                showToast(error.message || 'Error adding book', true);
            }
        }
        
        this.classList.add('was-validated');
    });
    
    // Toast notification function
    function showToast(message, isError = false) {
        const toastContainer = document.getElementById('toastContainer') || createToastContainer();
        const toast = document.createElement('div');
        toast.className = `toast show ${isError ? 'bg-danger text-white' : ''}`;
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
}); 