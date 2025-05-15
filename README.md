# Vintage Library

A retro-themed library management system with book browsing, reading list, and reviews.

## Features

- Book catalog browsing
- Reading list management
- Book reviews
- Responsive design
- Vintage aesthetic

## Setup Instructions

### Prerequisites

- XAMPP (or similar PHP/MySQL environment)
- Web browser
- Internet connection (for CDN resources)

### Installation

1. Clone or download this repository to your XAMPP `htdocs` directory.
2. Start Apache and MySQL services in XAMPP.
3. Open phpMyAdmin (usually at http://localhost/phpmyadmin).
4. Import the database by executing the SQL in the `database.sql` file.
5. Access the application at http://localhost/vintage-library.

### Database Setup

1. Open phpMyAdmin in your browser.
2. Click on "SQL" tab.
3. Copy the content of the `database.sql` file.
4. Paste it into the SQL query box and click "Go" to execute.
5. The database and all necessary tables will be created with sample data.

## Database Structure

The application uses MySQL database with the following tables:

1. **books** - Stores all book information
   - id, title, author, genre, publish_date, image_url, etc.

2. **reading_list** - Manages users' reading lists
   - id, book_id, total_pages, current_page, completed, etc.

3. **reviews** - Stores book reviews
   - id, book_id, reviewer_name, rating, comment, etc.

## API Endpoints

The following API endpoints are available:

### Books API (src/api/books.php)
- GET: Retrieve all books or a specific book by ID
- POST: Add a new book
- PUT: Update an existing book
- DELETE: Remove a book

### Reading List API (src/api/reading-list.php)
- GET: Retrieve the reading list
- POST: Add a book to the reading list
- PUT: Update reading progress
- DELETE: Remove a book from the reading list

### Reviews API (src/api/reviews.php)
- GET: Retrieve all reviews or reviews for a specific book
- POST: Add a new review
- PUT: Update an existing review
- DELETE: Remove a review

## Credits

- Images from Pexels and Unsplash
- Icons from Lucide
- Fonts from Google Fonts

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Author

Your Name
