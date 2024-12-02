// At the beginning of the file
console.log('Initializing books data...');

// Initial books data
const initialBooks = [
    {
        id: '1',
        isbn: '9780141439518',
        title: 'Pride and Prejudice',
        author: 'Jane Austen',
        genre: 'Classic Romance',
        publicationDate: '1813',
        copiesAvailable: 3,
        imageUrl: 'https://images.pexels.com/photos/1907785/pexels-photo-1907785.jpeg?auto=compress&cs=tinysrgb&w=600',
        description: 'A masterpiece of wit and social observation, following the character of Elizabeth Bennet as she deals with issues of manners, upbringing, morality, education, and marriage in the society of the landed gentry of early 19th-century England.',
        pages: 432,
        language: 'English',
        publisher: 'T. Egerton, Whitehall'
    },
    {
        id: '2',
        isbn: '9780486280615',
        title: 'The Adventures of Sherlock Holmes',
        author: 'Arthur Conan Doyle',
        genre: 'Mystery',
        publicationDate: '1892',
        copiesAvailable: 2,
        imageUrl: 'https://images.pexels.com/photos/3747279/pexels-photo-3747279.jpeg?auto=compress&cs=tinysrgb&w=600',
        description: 'A collection of twelve short stories featuring the famous detective Sherlock Holmes and his friend Dr. Watson, first published in The Strand Magazine.',
        pages: 307,
        language: 'English',
        publisher: 'George Newnes'
    },
    {
        id: '3',
        isbn: '9780141439556',
        title: 'Wuthering Heights',
        author: 'Emily Brontë',
        genre: 'Gothic Fiction',
        publicationDate: '1847',
        copiesAvailable: 2,
        imageUrl: 'https://m.media-amazon.com/images/I/81unikMK30L._AC_UF1000,1000_QL80_.jpg',
        description: 'A tale of passionate love between Heathcliff and Catherine Earnshaw, a foundling adopted by Catherine\'s father.',
        pages: 342,
        language: 'English',
        publisher: 'Thomas Cautley Newby'
    },
    {
        id: '4',
        isbn: '9780141439846',
        title: 'Jane Eyre',
        author: 'Charlotte Brontë',
        genre: 'Gothic Romance',
        publicationDate: '1847',
        copiesAvailable: 4,
        imageUrl: 'https://images.unsplash.com/photo-1544967082-d9d25d867d66?auto=format&fit=crop&q=80&w=400',
        description: 'The story follows the experiences of its eponymous heroine, including her growth to adulthood and her love for Mr. Rochester, the brooding master of Thornfield Hall.',
        pages: 532,
        language: 'English',
        publisher: 'Smith, Elder & Co.'
    },
    {
        id: '5',
        isbn: '9780141439693',
        title: 'Frankenstein',
        author: 'Mary Shelley',
        genre: 'Gothic Fiction',
        publicationDate: '1818',
        copiesAvailable: 2,
        imageUrl: 'https://images.unsplash.com/photo-1534447677768-be436bb09401?auto=format&fit=crop&q=80&w=400',
        description: 'The story of Victor Frankenstein, a young scientist who creates a sapient creature in an unorthodox scientific experiment, exploring themes of ambition, science, and humanity.',
        pages: 280,
        language: 'English',
        publisher: 'Lackington, Hughes, Harding, Mavor & Jones'
    },
    {
        id: '6',
        isbn: '9780141439587',
        title: 'Dracula',
        author: 'Bram Stoker',
        genre: 'Gothic Horror',
        publicationDate: '1897',
        copiesAvailable: 3,
        imageUrl: 'https://i.redd.it/614puazr39q91.jpg',
        description: 'The novel tells the story of Dracula\'s attempt to move from Transylvania to England so that he may find new blood and spread the undead curse, and of the battle between Dracula and a small group of people led by Professor Abraham Van Helsing.',
        pages: 418,
        language: 'English',
        publisher: 'Archibald Constable and Company'
    },
    {
        id: '7',
        isbn: '9780141439471',
        title: 'Little Women',
        author: 'Louisa May Alcott',
        genre: 'Coming-of-age',
        publicationDate: '1868',
        copiesAvailable: 2,
        imageUrl: 'https://images.pexels.com/photos/5834/nature-grass-leaf-green.jpg?auto=compress&cs=tinysrgb&w=600',
        description: 'The story follows the lives of the four March sisters—Meg, Jo, Beth, and Amy—and details their passage from childhood to womanhood.',
        pages: 449,
        language: 'English',
        publisher: 'Roberts Brothers'
    },
    {
        id: '8',
        isbn: '9780141439488',
        title: 'The Picture of Dorian Gray',
        author: 'Oscar Wilde',
        genre: 'Gothic Fiction',
        publicationDate: '1890',
        copiesAvailable: 3,
        imageUrl: 'https://images.pexels.com/photos/5834/nature-grass-leaf-green.jpg?auto=compress&cs=tinysrgb&w=600',
        description: 'A philosophical novel that tells of a young man named Dorian Gray, the subject of a painting by artist Basil Hallward.',
        pages: 254,
        language: 'English',
        publisher: 'Ward, Lock and Company'
    },
    {
        id: '9',
        isbn: '9780141439495',
        title: 'The Secret Garden',
        author: 'Frances Hodgson Burnett',
        genre: "Children's Literature",
        publicationDate: '1911',
        copiesAvailable: 4,
        imageUrl: 'https://images.pexels.com/photos/5834/nature-grass-leaf-green.jpg?auto=compress&cs=tinysrgb&w=600',
        description: 'A novel about a young girl who discovers a neglected garden and helps to bring it back to life.',
        pages: 331,
        language: 'English',
        publisher: 'Frederick A. Stokes'
    }
];

// Add this to your existing data.js or create it if it doesn't exist
const sampleBooks = [
    {
        id: '1',
        title: 'Pride and Prejudice',
        author: 'Jane Austen',
        // ... other book details
    },
    {
        id: '2',
        title: 'The Great Gatsby',
        author: 'F. Scott Fitzgerald',
        // ... other book details
    },
    // Add more books as needed
];

// Initialize books in localStorage if not present
if (!localStorage.getItem('books')) {
    localStorage.setItem('books', JSON.stringify(sampleBooks));
}

// Function to get all books
function getAllBooks() {
    return JSON.parse(localStorage.getItem('books') || '[]');
}

// Function to add a new book
function addBook(newBook) {
    const books = getAllBooks();
    // Generate a new ID
    newBook.id = (Math.max(...books.map(b => parseInt(b.id))) + 1).toString();
    books.push(newBook);
    localStorage.setItem('books', JSON.stringify(books));
    return newBook;
}

// Export functions and data
window.booksData = {
    addBook,
    getAllBooks,
    books: getAllBooks()
}; 