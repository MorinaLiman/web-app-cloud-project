<?php

function seedLibraryStoreData(PDO $pdo): void
{
    static $seeded = false;

    if ($seeded) {
        return;
    }

    $seeded = true;

    $pdo->exec('CREATE TABLE IF NOT EXISTS site_meta (
        meta_key VARCHAR(100) NOT NULL PRIMARY KEY,
        meta_value TEXT NOT NULL,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )');

    $desiredCategories = [
        'Letersi klasike',
        'Letersi moderne',
        'Biznes',
        'Programim',
        'Te tjera',
    ];

    $categoryIds = [];
    foreach ($desiredCategories as $index => $categoryName) {
        $stmt = $pdo->prepare('SELECT id FROM categories WHERE name = ? LIMIT 1');
        $stmt->execute([$categoryName]);
        $categoryId = $stmt->fetchColumn();

        if (!$categoryId) {
            $insertCategory = $pdo->prepare('INSERT INTO categories (name) VALUES (?)');
            $insertCategory->execute([$categoryName]);
            $categoryId = (int)$pdo->lastInsertId();
        }

        $categoryIds[$categoryName] = (int)$categoryId;
    }

    $orderColumns = [
        'buyer_full_name' => 'VARCHAR(150) NOT NULL',
        'buyer_first_name' => 'VARCHAR(80) NOT NULL',
        'buyer_last_name' => 'VARCHAR(80) NOT NULL',
        'buyer_account_email' => 'VARCHAR(190) NOT NULL',
        'payment_method' => "VARCHAR(50) DEFAULT 'credit_card'",
        'payment_cardholder_name' => 'VARCHAR(150) DEFAULT NULL',
        'payment_card_last4' => 'VARCHAR(4) DEFAULT NULL',
        'payment_status' => "VARCHAR(50) DEFAULT 'pending'",
    ];

    foreach ($orderColumns as $columnName => $definition) {
        $columnStmt = $pdo->prepare('SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = ? AND column_name = ?');
        $columnStmt->execute(['orders', $columnName]);

        if ((int)$columnStmt->fetchColumn() === 0) {
            $pdo->exec(sprintf('ALTER TABLE `orders` ADD COLUMN `%s` %s', $columnName, $definition));
        }
    }

    $featuredBooks = [
        [
            'title' => 'Pride and Prejudice',
            'author' => 'Jane Austen',
            'price' => 18.90,
            'year' => 1813,
            'isbn' => 'FTR-001-1813',
            'image' => '1.jfif',
            'description' => 'A classic novel about love, prejudice, and the social choices that shape the lives of the characters.',
            'category' => 'Letersi klasike',
        ],
        [
            'title' => '1984',
            'author' => 'George Orwell',
            'price' => 19.50,
            'year' => 1949,
            'isbn' => 'FTR-002-1949',
            'image' => '2.jfif',
            'description' => 'A dystopian novel about control, propaganda, and a society where personal freedom is suppressed.',
            'category' => 'Letersi moderne',
        ],
        [
            'title' => 'To Kill a Mockingbird',
            'author' => 'Harper Lee',
            'price' => 20.40,
            'year' => 1960,
            'isbn' => 'FTR-003-1960',
            'image' => '3.jfif',
            'description' => 'A powerful story about justice, empathy, and the growth of a child in a divided society.',
            'category' => 'Letersi klasike',
        ],
        [
            'title' => 'Ahab and the White Whale',
            'author' => 'Manuel Marsol',
            'price' => 17.60,
            'year' => 2021,
            'isbn' => 'FTR-004-2021',
            'image' => '5.jfif',
            'description' => 'An artistic and symbolic version of the legend of Ahab and the white whale, focused on obsession and adventure.',
            'category' => 'Te tjera',
        ],
        [
            'title' => 'Crime and Punishment',
            'author' => 'Fyodor Dostoevsky',
            'price' => 21.90,
            'year' => 1866,
            'isbn' => 'FTR-005-1866',
            'image' => '7.jfif',
            'description' => 'A psychological novel about guilt, morality, and the consequences of an extreme decision.',
            'category' => 'Letersi klasike',
        ],
        [
            'title' => 'The Catcher in the Rye',
            'author' => 'J.D. Salinger',
            'price' => 18.20,
            'year' => 1951,
            'isbn' => 'FTR-006-1951',
            'image' => '8.jfif',
            'description' => 'A novel about growing up, insecurity, and the feeling of loneliness during adolescence.',
            'category' => 'Letersi moderne',
        ],
    ];

    $syncIds = $pdo->query('SELECT id FROM products ORDER BY created_at DESC, id DESC LIMIT 6')->fetchAll(PDO::FETCH_COLUMN);
    if ($syncIds) {
        $updateBook = $pdo->prepare('UPDATE products SET title = ?, author = ?, price = ?, year_published = ?, isbn = ?, image_url = ?, description = ?, category_id = ? WHERE id = ?');

        foreach ($syncIds as $index => $productId) {
            if (!isset($featuredBooks[$index])) {
                break;
            }

            $book = $featuredBooks[$index];
            $updateBook->execute([
                $book['title'],
                $book['author'],
                $book['price'],
                $book['year'],
                $book['isbn'],
                '/Projekti/img/' . $book['image'],
                $book['description'],
                $categoryIds[$book['category']] ?? null,
                (int)$productId,
            ]);
        }
    }

    $currentCount = (int)$pdo->query('SELECT COUNT(*) FROM products')->fetchColumn();
    if ($currentCount < count($featuredBooks)) {
        $insertBook = $pdo->prepare('INSERT INTO products (title, author, price, year_published, isbn, image_url, description, category_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');

        for ($index = $currentCount; $index < count($featuredBooks); $index++) {
            $book = $featuredBooks[$index];
            $insertBook->execute([
                $book['title'],
                $book['author'],
                $book['price'],
                $book['year'],
                $book['isbn'],
                '/Projekti/img/' . $book['image'],
                $book['description'],
                $categoryIds[$book['category']] ?? null,
            ]);
        }
    }

    $markerStmt = $pdo->prepare('SELECT meta_value FROM site_meta WHERE meta_key = ? LIMIT 1');
    $markerStmt->execute(['librarystore_seed_featured_books_v1']);
    if ($markerStmt->fetchColumn() === false) {
        $markStmt = $pdo->prepare('INSERT INTO site_meta (meta_key, meta_value) VALUES (?, ?)');
        $markStmt->execute(['librarystore_seed_featured_books_v1', 'done']);
    }
}