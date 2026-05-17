<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/admin-flash.php';

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: /Projekti/admin-login.php');
    exit;
}

$pageTitle = 'Admin Dashboard';
$pageCss = 'admin.css';

$productCount = $pdo->query('SELECT COUNT(*) FROM products')->fetchColumn();
$categoryCount = $pdo->query('SELECT COUNT(*) FROM categories')->fetchColumn();
$messageCount = $pdo->query('SELECT COUNT(*) FROM messages')->fetchColumn();
$orderCount = $pdo->query('SELECT COUNT(*) FROM orders')->fetchColumn();
$cartCount = $pdo->query('SELECT COUNT(*) FROM cart')->fetchColumn();
$adminCount = $pdo->query('SELECT COUNT(*) FROM admins')->fetchColumn();
$userCount = $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
$orderItemCount = $pdo->query('SELECT COUNT(*) FROM order_items')->fetchColumn();
$siteMetaCount = $pdo->query('SELECT COUNT(*) FROM site_meta')->fetchColumn();
$flashSuccess = getAdminFlash();

include __DIR__ . '/../includes/header.php';
?>
<section class="container admin-page">
    <div class="admin-hero">
        <div>
            <div class="page-brand-block">
                <span class="logo-mark"><i class="fa fa-book" aria-hidden="true"></i></span>
                <div>
                    <strong>LibraryStore</strong>
                    <p>Admin Panel</p>
                </div>
            </div>
            <h2 class="section-title">Paneli i administratorit</h2>
            <p>Menaxho te gjitha tabelat kryesore te databazes nga nje vend i vetem.</p>
        </div>
        <div class="admin-hero-meta">
            <strong><?php echo (int)$productCount; ?></strong>
            <span>produkte aktive</span>
        </div>
    </div>
    <?php if ($flashSuccess): ?>
        <p class="admin-flash success"><?php echo htmlspecialchars($flashSuccess); ?></p>
    <?php endif; ?>
    <div class="grid admin-grid">
        <div class="card">
            <h3>Produkte</h3>
            <p><?php echo (int)$productCount; ?> produkte ne databaze.</p>
            <a class="btn-primary" href="/Projekti/admin/products.php">Menaxho produktet</a>
        </div>
        <div class="card">
            <h3>Kategori</h3>
            <p><?php echo (int)$categoryCount; ?> kategori aktive.</p>
            <a class="btn-primary" href="/Projekti/admin/categories.php">Menaxho kategorite</a>
        </div>
        <div class="card">
            <h3>Mesazhe</h3>
            <p><?php echo (int)$messageCount; ?> mesazhe nga kontakto forma.</p>
            <a class="btn-primary" href="/Projekti/admin/messages.php">Shiko mesazhet</a>
        </div>
        <div class="card">
            <h3>Porosi</h3>
            <p><?php echo (int)$orderCount; ?> porosi te ruajtura me kohe dhe page.</p>
            <a class="btn-primary" href="/Projekti/admin/orders.php">Menaxho porosite</a>
        </div>
        <div class="card">
            <h3>Shporta</h3>
            <p><?php echo (int)$cartCount; ?> libra ne shporte para pageses.</p>
            <a class="btn-primary" href="/Projekti/admin/cart.php">Menaxho shporten</a>
        </div>
        <div class="card">
            <h3>Perdorues</h3>
            <p><?php echo (int)$userCount; ?> perdorues te regjistruar.</p>
            <a class="btn-primary" href="/Projekti/admin/users.php">Menaxho perdoruesit</a>
        </div>
        <div class="card">
            <h3>Administratore</h3>
            <p><?php echo (int)$adminCount; ?> llogari admin.</p>
            <a class="btn-primary" href="/Projekti/admin/admins.php">Menaxho adminet</a>
        </div>
        <div class="card">
            <h3>Order Items</h3>
            <p><?php echo (int)$orderItemCount; ?> artikuj ne porosi.</p>
            <a class="btn-primary" href="/Projekti/admin/order-items.php">Menaxho item-et</a>
        </div>
        <div class="card">
            <h3>Site Meta</h3>
            <p><?php echo (int)$siteMetaCount; ?> meta te ruajtura.</p>
            <a class="btn-primary" href="/Projekti/admin/site-meta.php">Menaxho meta</a>
        </div>
    </div>
    <div class="admin-header" style="margin-top:24px;">
        <a class="btn-secondary" href="/Projekti/admin/dashboard.php?logout=1">Dil</a>
    </div>
</section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
