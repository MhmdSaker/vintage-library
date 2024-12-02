document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('contactForm');
    if (form) {
        form.addEventListener('submit', function(event) {
            event.preventDefault();
            
            // In a real application, you would send this to a server
            alert('Thank you for your message! We will get back to you soon.');
            form.reset();
        });
    }
}); 