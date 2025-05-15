document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('addBookForm');

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
                        window.location.href = 'index.html';
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