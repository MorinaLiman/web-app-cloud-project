CREATE DATABASE IF NOT EXISTS bookstore_db;
USE bookstore_db;

CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
);

CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    author VARCHAR(150) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    year_published INT NOT NULL,
    isbn VARCHAR(50) NOT NULL,
    image_url VARCHAR(500) NOT NULL,
    description TEXT NOT NULL,
    category_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(150) NOT NULL,
    subject VARCHAR(150) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(150) NOT NULL,
    email VARCHAR(190) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    buyer_full_name VARCHAR(150) NOT NULL,
    buyer_first_name VARCHAR(80) NOT NULL,
    buyer_last_name VARCHAR(80) NOT NULL,
    buyer_account_email VARCHAR(190) NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    status VARCHAR(50) DEFAULT 'pending',
    payment_date DATE DEFAULT NULL,
    payment_time TIME DEFAULT NULL,
    payment_method VARCHAR(50) DEFAULT 'credit_card',
    payment_cardholder_name VARCHAR(150) DEFAULT NULL,
    payment_card_last4 VARCHAR(4) DEFAULT NULL,
    payment_status VARCHAR(50) DEFAULT 'pending',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
);

INSERT INTO categories (name) VALUES
('Letersi klasike'),
('Letersi moderne'),
('Biznes'),
('Programim'),
('Te tjera');

INSERT INTO products (title, author, price, year_published, isbn, image_url, description, category_id) VALUES
('Pride and Prejudice', 'Jane Austen', 18.90, 1813, 'FTR-001-1813', '/Projekti/img/1.jfif', 'A classic novel about love, prejudice, and the social choices that affect the lives of the characters.', 1),
('1984', 'George Orwell', 19.50, 1949, 'FTR-002-1949', '/Projekti/img/2.jfif', 'A dystopian novel about control, propaganda, and a society where personal freedoms are suppressed.', 2),
('To Kill a Mockingbird', 'Harper Lee', 20.40, 1960, 'FTR-003-1960', '/Projekti/img/3.jfif', 'A powerful story about justice, empathy, and the growth of a child in a divided society.', 1),
('Ahab and the White Whale', 'Manuel Marsol', 17.60, 2021, 'FTR-004-2021', '/Projekti/img/5.jfif', 'An artistic and symbolic version of the legend of Ahab and the white whale, focusing on obsession and adventure.', 5),
('Crime and Punishment', 'Fyodor Dostoevsky', 21.90, 1866, 'FTR-005-1866', '/Projekti/img/7.jfif', 'A psychological novel about guilt, morality, and the consequences of an extreme decision.', 1),
('The Catcher in the Rye', 'J.D. Salinger', 18.20, 1951, 'FTR-006-1951', '/Projekti/img/8.jfif', 'A novel about growing up, insecurity, and the feeling of loneliness during adolescence.', 2);
