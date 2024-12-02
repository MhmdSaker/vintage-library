const express = require('express');
const fs = require('fs').promises;
const path = require('path');
const app = express();

app.use(express.json());
app.use(express.static('.'));

app.post('/api/books', async (req, res) => {
    try {
        const booksPath = path.join(__dirname, 'src/data/books.json');
        const booksData = await fs.readFile(booksPath, 'utf8');
        const books = JSON.parse(booksData);
        
        const lastId = books.books[books.books.length - 1].id;
        const newBook = {
            id: String(parseInt(lastId) + 1),
            ...req.body
        };
        
        books.books.push(newBook);
        
        await fs.writeFile(booksPath, JSON.stringify(books, null, 2));
        
        res.json({ success: true, message: 'Book added successfully' });
    } catch (error) {
        res.status(500).json({ success: false, message: error.message });
    }
});

app.listen(3000, () => {
    console.log('Server running on port 3000');
}); 