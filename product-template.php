<?php
require_once __DIR__ . '/db.php';

$stmt = $pdo->prepare('SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.id = ?');
$stmt->execute([$productId]);
$product = $stmt->fetch();

$pageTitle = $product ? $product['title'] : 'Produkt';
$pageCss = 'product.css';
$activePage = 'catalog';
include __DIR__ . '/header.php';
?>
<section class="product-hero">
    <div class="container product-grid">
        <?php if ($product): ?>
            <div class="product-cover">
                <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['title']); ?>">
            </div>
            <div class="product-info">
                <span class="tag"><?php echo htmlspecialchars($product['category_name'] ?? 'Libra'); ?></span>
                <h1><?php echo htmlspecialchars($product['title']); ?></h1>
                <p class="author"><?php echo htmlspecialchars($product['author']); ?></p>
                <p class="description"><?php echo htmlspecialchars($product['description']); ?></p>
                <div class="meta">
                    <div><strong>Cmimi:</strong> <?php echo number_format($product['price'], 2); ?> EUR</div>
                    <div><strong>Viti:</strong> <?php echo (int)$product['year_published']; ?></div>
                    <div><strong>ISBN:</strong> <?php echo htmlspecialchars($product['isbn']); ?></div>
                </div>
                <form method="post" action="/Projekti/cart.php" class="inline-cart-form">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="product_id" value="<?php echo (int)$productId; ?>">
                    <input type="hidden" name="quantity" value="1">
                    <button class="btn-primary" type="submit">Shto ne shporte</button>
                </form>
            </div>
        <?php else: ?>
            <div class="empty-state">Produkti nuk u gjet.</div>
        <?php endif; ?>
    </div>
</section>
<?php include __DIR__ . '/footer.php'; ?>
