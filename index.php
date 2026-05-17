<?php

require_once __DIR__ . '/includes/db.php';

$pageTitle = 'LibraryStore - Shtepi';
$pageCss = 'index.css';
$extraPageCss = 'catalog.css';
$activePage = 'home';

try {
    $latestStmt = $pdo->query("
        SELECT 
            p.id, 
            p.title, 
            p.author, 
            p.price, 
            p.image_url, 
            c.name AS category_name, 
            p.description 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        ORDER BY p.created_at DESC 
        LIMIT 6
    ");

    $latestBooks = $latestStmt->fetchAll();

} catch (PDOException $e) {
    die("Query error: " . $e->getMessage());
}

include __DIR__ . '/views/index.view.php';