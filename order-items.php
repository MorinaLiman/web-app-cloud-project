<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/admin-flash.php';

$pageTitle = 'Admin Order Items';
$pageCss = 'admin.css';
$pageJs = 'admin.js';

if (isset($_POST['action']) && $_POST['action'] === 'save') {
    $id = (int)($_POST['id'] ?? 0);
    $orderId = (int)($_POST['order_id'] ?? 0);
    $productId = (int)($_POST['product_id'] ?? 0);
    $quantity = max(1, (int)($_POST['quantity'] ?? 1));
    $price = (float)($_POST['price'] ?? 0);

    if ($orderId <= 0 || $productId <= 0) {
        setAdminFlash('Zgjidh porosine dhe produktin.');
        header('Location: /Projekti/admin/order-items.php');
        exit;
    }

    if ($price <= 0) {
        $stmt = $pdo->prepare('SELECT price FROM products WHERE id = ?');
        $stmt->execute([$productId]);
        $price = (float)$stmt->fetchColumn();
    }

    if ($id > 0) {
        $stmt = $pdo->prepare('UPDATE order_items SET order_id = ?, product_id = ?, quantity = ?, price = ? WHERE id = ?');
        $stmt->execute([$orderId, $productId, $quantity, $price, $id]);
        setAdminFlash('Order item u ndryshua me sukses.');
    } else {
        $stmt = $pdo->prepare('INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)');
        $stmt->execute([$orderId, $productId, $quantity, $price]);
        setAdminFlash('Order item u shtua me sukses.');
    }

    header('Location: /Projekti/admin/order-items.php');
    exit;
}

if (isset($_GET['delete'])) {
    $deleteId = (int)$_GET['delete'];
    $stmt = $pdo->prepare('DELETE FROM order_items WHERE id = ?');
    $stmt->execute([$deleteId]);
    setAdminFlash('Order item u fshi me sukses.');
    header('Location: /Projekti/admin/order-items.php');
    exit;
}

$editItem = null;
if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    $stmt = $pdo->prepare('SELECT * FROM order_items WHERE id = ?');
    $stmt->execute([$editId]);
    $editItem = $stmt->fetch();
}

$orders = $pdo->query('SELECT id, buyer_full_name, buyer_account_email, created_at FROM orders ORDER BY created_at DESC')->fetchAll();
$products = $pdo->query('SELECT id, title, author, price FROM products ORDER BY title')->fetchAll();
$orderItems = $pdo->query(
    'SELECT oi.*, p.title, p.author, o.buyer_full_name, o.buyer_account_email
     FROM order_items oi
     JOIN products p ON p.id = oi.product_id
     JOIN orders o ON o.id = oi.order_id
     ORDER BY oi.id DESC'
)->fetchAll();
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
            <h2 class="section-title">Menaxho order items</h2>
            <p>Rregullo artikujt e lidhur me porosite dhe detajet e tyre.</p>
        </div>
        <a class="btn-secondary" href="/Projekti/admin/dashboard.php">Dashboard</a>
    </div>

    <?php if ($flashSuccess): ?>
        <p class="admin-flash success"><?php echo htmlspecialchars($flashSuccess); ?></p>
    <?php endif; ?>

    <form class="card admin-form" method="post">
        <input type="hidden" name="action" value="save">
        <input type="hidden" name="id" value="<?php echo (int)($editItem['id'] ?? 0); ?>">

        <select name="order_id" required>
            <option value="">Zgjidh porosine</option>
            <?php foreach ($orders as $order): ?>
                <option value="<?php echo (int)$order['id']; ?>" <?php echo isset($editItem['order_id']) && (int)$editItem['order_id'] === (int)$order['id'] ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars('#' . $order['id'] . ' - ' . $order['buyer_full_name'] . ' (' . $order['buyer_account_email'] . ')'); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select name="product_id" required>
            <option value="">Zgjidh produktin</option>
            <?php foreach ($products as $product): ?>
                <option value="<?php echo (int)$product['id']; ?>" <?php echo isset($editItem['product_id']) && (int)$editItem['product_id'] === (int)$product['id'] ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($product['title'] . ' - ' . $product['author']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <input type="number" name="quantity" min="1" value="<?php echo htmlspecialchars((string)($editItem['quantity'] ?? 1)); ?>" required>
        <input type="number" step="0.01" name="price" placeholder="Cmimi per njesi" value="<?php echo htmlspecialchars((string)($editItem['price'] ?? '')); ?>">
        <button class="btn-primary" type="submit"><?php echo $editItem ? 'Ruaj ndryshimet' : 'Shto item'; ?></button>
    </form>

    <table class="table">
        <thead>
            <tr>
                <th>Order</th>
                <th>Klienti</th>
                <th>Produkti</th>
                <th>Sasia</th>
                <th>Cmimi</th>
                <th>Subtotal</th>
                <th>Veprime</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orderItems as $item): ?>
                <tr>
                    <td>#<?php echo (int)$item['order_id']; ?></td>
                    <td>
                        <?php echo htmlspecialchars($item['buyer_full_name']); ?><br>
                        <small><?php echo htmlspecialchars($item['buyer_account_email']); ?></small>
                    </td>
                    <td><?php echo htmlspecialchars($item['title']); ?><br><small><?php echo htmlspecialchars($item['author']); ?></small></td>
                    <td><?php echo (int)$item['quantity']; ?></td>
                    <td><?php echo number_format((float)$item['price'], 2); ?> EUR</td>
                    <td><?php echo number_format((float)$item['price'] * (int)$item['quantity'], 2); ?> EUR</td>
                    <td>
                        <a href="/Projekti/admin/order-items.php?edit=<?php echo (int)$item['id']; ?>">Edit</a>
                        <a data-confirm="A jeni i sigurt?" href="/Projekti/admin/order-items.php?delete=<?php echo (int)$item['id']; ?>">Fshi</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
