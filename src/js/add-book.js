document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('addBookForm');

    form.addEventListener('submit', function(e) {
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
                pages: parseInt(formData.get('pages')) || 100,
                language: formData.get('language') || 'English',
                publisher: formData.get('publisher') || 'Unknown Publisher'
            };

            // Add the book using the data.js function
            window.booksData.addBook(newBook);
            
            // Show success message
            showToast('Book added successfully!');
            
            // Reset form
            form.reset();
            
            // Redirect to homepage after short delay
            setTimeout(() => {
                window.location.href = 'index.html';
            }, 2000);
        }
        
        this.classList.add('was-validated');
    });
}); 