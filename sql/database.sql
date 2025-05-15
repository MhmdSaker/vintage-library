-- Create the database
CREATE DATABASE IF NOT EXISTS vintage_library;
USE vintage_library;

-- Books table
CREATE TABLE IF NOT EXISTS books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(255) NOT NULL,
    genre VARCHAR(100) NOT NULL,
    publish_date VARCHAR(50),
    image_url TEXT,
    copies_available INT DEFAULT 1,
    description TEXT,
    `condition` VARCHAR(50),
    language VARCHAR(50),
    format VARCHAR(50),
    is_favorite BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    isbn VARCHAR(20) NULL
);

-- Reading List table
CREATE TABLE IF NOT EXISTS reading_list (
    id INT AUTO_INCREMENT PRIMARY KEY,
    book_id INT NOT NULL,
    total_pages INT DEFAULT 1,
    current_page INT DEFAULT 0,
    completed BOOLEAN DEFAULT FALSE,
    date_added TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE
);

-- Reviews table
CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    book_id INT NOT NULL,
    reviewer_name VARCHAR(100) NOT NULL,
    rating INT NOT NULL,
    comment TEXT,
    review_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE
);

-- Insert initial books data
INSERT INTO books (id, title, author, genre, publish_date, image_url, copies_available, description, `condition`, language, format, is_favorite) VALUES
(1, 'Pride and Prejudice', 'Jane Austen', 'Classic Romance', '1813', 'https://images.pexels.com/photos/1907785/pexels-photo-1907785.jpeg?auto=compress&w=800', 3, 'A classic tale of love and social manners in Georgian England.', 'Good', 'English', 'Hardcover', FALSE),
(2, 'Jane Eyre', 'Charlotte Brontë', 'Gothic Fiction', '1847', 'https://images.pexels.com/photos/3747468/pexels-photo-3747468.jpeg?auto=compress&w=800', 2, 'A Gothic romance following the life of its eponymous heroine.', 'Excellent', 'English', 'Leather Bound', TRUE),
(3, 'Wuthering Heights', 'Emily Brontë', 'Gothic Fiction', '1847', 'https://images.pexels.com/photos/12148639/pexels-photo-12148639.jpeg?auto=compress&cs=tinysrgb&w=600', 1, 'A tale of passionate love and revenge on the Yorkshire moors.', 'Good', 'English', 'Hardcover', TRUE),
(4, 'The Picture of Dorian Gray', 'Oscar Wilde', 'Gothic Fiction', '1890', 'https://images.pexels.com/photos/3389817/pexels-photo-3389817.jpeg?auto=compress&w=800', 4, 'A philosophical novel about beauty, decadence, and corruption.', 'Excellent', 'English', 'Leather Bound', FALSE),
(5, 'Little Women', 'Louisa May Alcott', 'Classic Romance', '1868', 'https://images.pexels.com/photos/3747279/pexels-photo-3747279.jpeg?auto=compress&w=800', 2, 'The story of four sisters growing up during the American Civil War.', 'Good', 'English', 'Hardcover', TRUE),
(6, 'The Secret Garden', 'Frances Hodgson Burnett', 'Children\'s Literature', '1911', 'https://images.pexels.com/photos/28801970/pexels-photo-28801970.jpeg?auto=compress&w=800', 3, 'A classic tale of a young girl who discovers a magical garden and its healing powers.', 'Good', 'English', 'Hardcover', FALSE),
(7, 'The Great Gatsby', 'F. Scott Fitzgerald', 'Literary Fiction', '1925', 'https://images.pexels.com/photos/2762083/pexels-photo-2762083.jpeg?auto=compress&w=800', 4, 'A tale of decadence, idealism, and excess in the Jazz Age.', 'Excellent', 'English', 'Leather Bound', FALSE),
(8, 'Emily of New Moon', 'L.M. Montgomery', 'Coming of Age', '1923', 'https://images.pexels.com/photos/29570340/pexels-photo-29570340.jpeg?auto=compress&w=800', 2, 'The story of an orphaned girl who dreams of becoming a writer.', 'Good', 'English', 'Hardcover', FALSE),
(9, 'The Hunchback of Notre-Dame', 'Victor Hugo', 'Gothic Fiction', '1831', 'https://images.pexels.com/photos/29570390/pexels-photo-29570390.jpeg?auto=compress&w=800', 1, 'A tragic tale of love and prejudice set in medieval Paris.', 'Good', 'English', 'Leather Bound', FALSE),
(10, 'The Name of the Rose', 'Umberto Eco', 'Historical Mystery', '1980', 'https://images.pexels.com/photos/29545914/pexels-photo-29545914.jpeg?auto=compress&w=800', 3, 'A medieval murder mystery set in an Italian monastery.', 'Excellent', 'English', 'Hardcover', FALSE),
(11, 'Doctor Zhivago', 'Boris Pasternak', 'Historical Fiction', '1957', 'https://images.pexels.com/photos/28217988/pexels-photo-28217988.jpeg?auto=compress&w=800', 2, 'A love story set against the backdrop of the Russian Revolution.', 'Good', 'English', 'Hardcover', FALSE),
(12, 'The Raven', 'Edgar Allan Poe', 'Gothic Poetry', '1845', 'https://images.pexels.com/photos/29540913/pexels-photo-29540913.jpeg?auto=compress&w=800', 5, 'A collection of Gothic poetry including the famous narrative poem \'The Raven\'.', 'Excellent', 'English', 'Leather Bound', FALSE),
(13, 'Rebecca', 'Daphne du Maurier', 'Gothic Romance', '1938', 'https://images.pexels.com/photos/4906457/pexels-photo-4906457.jpeg?auto=compress&w=800', 3, 'A haunting tale of romance and mystery at Manderley estate.', 'Good', 'English', 'Hardcover', FALSE),
(14, 'The Arabian Nights', 'Various Authors', 'Folk Tales', '8th-14th centuries', 'https://images.pexels.com/photos/17727326/pexels-photo-17727326.jpeg?auto=compress&w=800', 2, 'A collection of Middle Eastern folk tales compiled over many centuries.', 'Excellent', 'English', 'Leather Bound', FALSE),
(15, 'Anna Karenina', 'Leo Tolstoy', 'Literary Fiction', '1877', 'https://images.pexels.com/photos/15153893/pexels-photo-15153893.jpeg?auto=compress&w=800', 4, 'A tragic tale of love and society in imperial Russia.', 'Good', 'English', 'Hardcover', TRUE);

-- Insert initial reading list data
INSERT INTO reading_list (book_id, total_pages, current_page, completed, date_added) VALUES
(1, 3, 3, TRUE, '2024-12-02 19:08:40'),
(5, 3, 3, TRUE, '2024-12-02 19:14:02'),
(4, 7, 7, TRUE, '2024-12-02 20:54:02'),
(8, 2, 2, TRUE, '2024-12-03 12:07:20'),
(9, 5, 0, FALSE, '2024-12-03 12:07:25'),
(10, 4, 0, FALSE, '2024-12-21 18:50:31'); 