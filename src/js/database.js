// Initial Books Data
const initialBooks = [
    {
        id: '1',
        title: 'Pride and Prejudice',
        author: 'Jane Austen',
        genre: 'Classic Romance',
        description: 'A masterpiece of wit and social commentary, following the turbulent relationship between Elizabeth Bennet and Mr. Darcy.',
        publishDate: '1813-01-28',
        imageUrl: 'https://images.pexels.com/photos/1907785/pexels-photo-1907785.jpeg',
        copiesAvailable: 3,
        totalCopies: 5
    },
    {
        id: '2',
        title: 'Dracula',
        author: 'Bram Stoker',
        genre: 'Gothic Fiction',
        description: 'The classic vampire tale that defined the genre, told through letters and diary entries.',
        publishDate: '1897-05-26',
        imageUrl: 'https://images.pexels.com/photos/3646105/pexels-photo-3646105.jpeg',
        copiesAvailable: 2,
        totalCopies: 4
    },
    {
        id: '3',
        title: 'Jane Eyre',
        author: 'Charlotte Brontë',
        genre: 'Gothic Romance',
        description: 'A groundbreaking coming-of-age story following the emotions and experiences of its eponymous character.',
        publishDate: '1847-10-16',
        imageUrl: 'https://images.pexels.com/photos/3747279/pexels-photo-3747279.jpeg',
        copiesAvailable: 1,
        totalCopies: 3
    },
    {
        id: '4',
        title: 'The Picture of Dorian Gray',
        author: 'Oscar Wilde',
        genre: 'Gothic Fiction',
        description: 'A philosophical novel exploring the nature of beauty, art, and morality.',
        publishDate: '1890-07-01',
        imageUrl: 'https://images.pexels.com/photos/3747463/pexels-photo-3747463.jpeg',
        copiesAvailable: 4,
        totalCopies: 4
    },
    {
        id: '5',
        title: 'Wuthering Heights',
        author: 'Emily Brontë',
        genre: 'Gothic Romance',
        description: 'A tale of passionate love and revenge set in the Yorkshire moors.',
        publishDate: '1847-12-19',
        imageUrl: 'https://images.pexels.com/photos/3747511/pexels-photo-3747511.jpeg',
        copiesAvailable: 2,
        totalCopies: 3
    },
    {
        id: '6',
        title: 'The Adventures of Sherlock Holmes',
        author: 'Arthur Conan Doyle',
        genre: 'Mystery',
        description: 'A collection of twelve detective stories featuring the famous consulting detective.',
        publishDate: '1892-10-14',
        imageUrl: 'https://images.pexels.com/photos/3747548/pexels-photo-3747548.jpeg',
        copiesAvailable: 3,
        totalCopies: 5
    },
    {
        id: '7',
        title: 'The Great Gatsby',
        author: 'F. Scott Fitzgerald',
        genre: 'Literary Fiction',
        description: 'A tale of decadence and excess, exploring the American Dream in the Jazz Age through the eyes of Nick Carraway.',
        publishDate: '1925-04-10',
        imageUrl: 'https://images.pexels.com/photos/2177482/pexels-photo-2177482.jpeg',
        copiesAvailable: 4,
        totalCopies: 6
    },
    {
        id: '8',
        title: 'Moby Dick',
        author: 'Herman Melville',
        genre: 'Adventure',
        description: 'The epic tale of Captain Ahab\'s obsessive quest for revenge against the white whale Moby Dick.',
        publishDate: '1851-10-18',
        imageUrl: 'https://images.pexels.com/photos/1001682/pexels-photo-1001682.jpeg',
        copiesAvailable: 2,
        totalCopies: 4
    },
    {
        id: '9',
        title: 'Little Women',
        author: 'Louisa May Alcott',
        genre: 'Coming of Age',
        description: 'The heartwarming story of the four March sisters as they grow up in Civil War-era New England.',
        publishDate: '1868-09-30',
        imageUrl: 'https://images.pexels.com/photos/3747464/pexels-photo-3747464.jpeg',
        copiesAvailable: 3,
        totalCopies: 5
    },
    {
        id: '10',
        title: 'The Canterbury Tales',
        author: 'Geoffrey Chaucer',
        genre: 'Classic Literature',
        description: 'A collection of stories told by pilgrims on their way to Canterbury, offering a glimpse into medieval life.',
        publishDate: '1400-01-01',
        imageUrl: 'https://images.pexels.com/photos/5834/nature-grass-leaf-green.jpg',
        copiesAvailable: 2,
        totalCopies: 3
    },
    {
        id: '11',
        title: 'The Count of Monte Cristo',
        author: 'Alexandre Dumas',
        genre: 'Adventure',
        description: 'An epic tale of revenge and redemption following Edmond Dantès\' transformation into the mysterious Count.',
        publishDate: '1844-08-28',
        imageUrl: 'https://images.pexels.com/photos/3375997/pexels-photo-3375997.jpeg',
        copiesAvailable: 3,
        totalCopies: 4
    },
    {
        id: '12',
        title: 'The Scarlet Letter',
        author: 'Nathaniel Hawthorne',
        genre: 'Historical Fiction',
        description: 'A story of passion, guilt, and redemption in Puritan New England, centered around Hester Prynne.',
        publishDate: '1850-03-16',
        imageUrl: 'https://images.pexels.com/photos/5834/nature-grass-leaf-green.jpg',
        copiesAvailable: 4,
        totalCopies: 5
    },
    {
        id: '13',
        title: 'War and Peace',
        author: 'Leo Tolstoy',
        genre: 'Historical Fiction',
        description: 'An epic novel following five aristocratic families through the Napoleonic Era in Russia.',
        publishDate: '1869-01-01',
        imageUrl: 'https://images.pexels.com/photos/1205651/pexels-photo-1205651.jpeg',
        copiesAvailable: 2,
        totalCopies: 3
    },
    {
        id: '14',
        title: 'The Odyssey',
        author: 'Homer',
        genre: 'Epic Poetry',
        description: 'The ancient Greek epic following Odysseus\'s ten-year journey home after the Trojan War.',
        publishDate: '-800-01-01',
        imageUrl: 'https://images.pexels.com/photos/3680219/pexels-photo-3680219.jpeg',
        copiesAvailable: 3,
        totalCopies: 4
    },
    {
        id: '15',
        title: 'Don Quixote',
        author: 'Miguel de Cervantes',
        genre: 'Literary Fiction',
        description: 'The story of an elderly knight who loses his sanity and embarks on a series of quixotic adventures.',
        publishDate: '1605-01-16',
        imageUrl: 'https://images.pexels.com/photos/7034109/pexels-photo-7034109.jpeg',
        copiesAvailable: 2,
        totalCopies: 4
    }
];

// Database Class to handle all data operations
class Database {
    constructor() {
        this.books = [...initialBooks];
        this.borrowedBooks = [];
        this.favorites = [];
        this.reviews = [];
        this.events = [...defaultEvents];
    }

    // Books Operations
    getAllBooks() {
        return this.books;
    }

    searchBooks(query) {
        if (!query) return this.books;
        
        query = query.toLowerCase();
        return this.books.filter(book => 
            book.title.toLowerCase().includes(query) ||
            book.author.toLowerCase().includes(query) ||
            book.genre.toLowerCase().includes(query) ||
            (book.description && book.description.toLowerCase().includes(query))
        );
    }

    getBookById(id) {
        return this.books.find(book => book.id === id);
    }

    getBooksByGenre(genre) {
        return this.books.filter(book => book.genre === genre);
    }

    addBook(book) {
        book.id = (Math.max(...this.books.map(b => parseInt(b.id))) + 1).toString();
        this.books.push(book);
        return book;
    }

    updateBook(id, updatedBook) {
        const index = this.books.findIndex(book => book.id === id);
        if (index !== -1) {
            this.books[index] = { ...this.books[index], ...updatedBook };
            return true;
        }
        return false;
    }

    // Borrowed Books Operations
    getBorrowedBooks(userId) {
        return this.borrowedBooks.filter(item => item.userId === userId);
    }

    borrowBook(userId, bookId) {
        const book = this.getBookById(bookId);
        if (book && book.copiesAvailable > 0) {
            this.borrowedBooks.push({
                userId,
                bookId,
                borrowDate: new Date().toISOString(),
                dueDate: new Date(Date.now() + 14 * 24 * 60 * 60 * 1000).toISOString()
            });
            book.copiesAvailable--;
            return true;
        }
        return false;
    }

    returnBook(userId, bookId) {
        const index = this.borrowedBooks.findIndex(
            item => item.userId === userId && item.bookId === bookId
        );
        if (index !== -1) {
            this.borrowedBooks.splice(index, 1);
            const book = this.getBookById(bookId);
            if (book) {
                book.copiesAvailable++;
            }
        }
    }

    // Favorites Operations
    getFavorites(userId) {
        return this.favorites.filter(item => item.userId === userId);
    }

    toggleFavorite(userId, bookId) {
        const index = this.favorites.findIndex(
            item => item.userId === userId && item.bookId === bookId
        );
        if (index === -1) {
            this.favorites.push({
                userId,
                bookId,
                dateAdded: new Date().toISOString()
            });
        } else {
            this.favorites.splice(index, 1);
        }
    }

    // Reviews Operations
    getBookReviews(bookId) {
        return this.reviews.filter(review => review.bookId === bookId);
    }

    addReview(review) {
        review.id = Date.now().toString();
        review.date = new Date().toISOString();
        this.reviews.push(review);
        return review;
    }

    // Events Operations
    getAllEvents() {
        return this.events;
    }

    addEvent(event) {
        event.id = Date.now().toString();
        this.events.push(event);
        return event;
    }
}

// Default Events Data
const defaultEvents = [
    {
        id: '1',
        title: 'Book Club Meeting',
        description: 'Join us for an engaging discussion of Jane Austen\'s Pride and Prejudice',
        date: '2024-03-15',
        time: '14:00',
        location: 'Main Reading Room',
        type: 'Book Club',
        book: 'Pride and Prejudice',
        maxParticipants: 20,
        currentParticipants: 12
    },
    {
        id: '2',
        title: 'Author Meet & Greet',
        description: 'Meet the author of "The Lost Letters" and get your book signed',
        date: '2024-03-20',
        time: '18:00',
        location: 'Events Hall',
        type: 'Author Event',
        guest: 'Sarah Mitchell',
        maxParticipants: 50,
        currentParticipants: 25
    },
    {
        id: '3',
        title: 'Poetry Reading Night',
        description: 'An evening of classic Victorian poetry readings and discussion',
        date: '2024-03-25',
        time: '19:00',
        location: 'Poetry Corner',
        type: 'Reading',
        theme: 'Victorian Poetry',
        maxParticipants: 30,
        currentParticipants: 15
    }
];

// Create and export database instance
window.db = new Database(); 