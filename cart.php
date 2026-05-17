<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/admin-flash.php';

$pageTitle = 'Admin Shporta';
$pageCss = 'admin.css';
$pageJs = 'admin.js';

if (isset($_POST['action']) && $_POST['action'] === 'save') {
    $id = (int)($_POST['id'] ?? 0);
    $userId = (int)($_POST['user_id'] ?? 0);
    $productId = (int)($_POST['product_id'] ?? 0);
    $quantity = max(1, (int)($_POST['quantity'] ?? 1));

    if ($id > 0) {
        $stmt = $pdo->prepare('UPDATE cart SET user_id = ?, product_id = ?, quantity = ? WHERE id = ?');
        $stmt->execute([$userId, $productId, $quantity, $id]);
        setAdminFlash('Shporta u ndryshua me sukses.');
    } else {
        $stmt = $pdo->prepare('INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)');
        $stmt->execute([$userId, $productId, $quantity]);
        setAdminFlash('Shporta u shtua me sukses.');
    }

    header('Location: /Projekti/admin/cart.php');
    exit;
}

if (isset($_GET['delete'])) {
    $deleteId = (int)$_GET['delete'];
    $stmt = $pdo->prepare('DELETE FROM cart WHERE id = ?');
    $stmt->execute([$deleteId]);
    setAdminFlash('Shporta u fshi me sukses.');
    header('Location: /Projekti/admin/cart.php');
    exit;
}

$editCart = null;
if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    $stmt = $pdo->prepare('SELECT * FROM cart WHERE id = ?');
    $stmt->execute([$editId]);
    $editCart = $stmt->fetch();
}

$users = $pdo->query('SELECT id, full_name, email FROM users ORDER BY full_name')->fetchAll();
$products = $pdo->query('SELECT id, title, author FROM products ORDER BY title')->fetchAll();
$cartRows = $pdo->query(
    'SELECT c.*, u.full_name, u.email, p.title, p.author, p.price
     FROM cart c
     JOIN users u ON u.id = c.user_id
     JOIN products p ON p.id = c.product_id
     ORDER BY c.added_at DESC'
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
            <h2 class="section-title">Menaxho shporten</h2>
            <p>Kontrollo librat e shtuar para pageses dhe ruaj strukturen e qarte te blerjes.</p>
        </div>
        <a class="btn-secondary" href="/Projekti/admin/dashboard.php">Dashboard</a>
    </div>

    <?php if ($flashSuccess): ?>
        <p class="admin-flash success"><?php echo htmlspecialchars($flashSuccess); ?></p>
    <?php endif; ?>

    <div class="card" style="margin-bottom:24px; padding:18px; border-radius:18px; background:rgba(47,107,255,0.06);">
        <strong>Qellimi:</strong> Kjo tabelë tregon librat e shtuar nga useri para pageses. Mund t'i ndryshosh sasine, librin, userin ose t'i fshish kur te duhet.
    </div>

    <form class="card admin-form" method="post">
        <input type="hidden" name="action" value="save">
        <input type="hidden" name="id" value="<?php echo (int)($editCart['id'] ?? 0); ?>">

        <select name="user_id" required>
            <option value="">Zgjidh userin</option>
            <?php foreach ($users as $user): ?>
                <option value="<?php echo (int)$user['id']; ?>" <?php echo isset($editCart['user_id']) && (int)$editCart['user_id'] === (int)$user['id'] ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($user['full_name'] . ' (' . $user['email'] . ')'); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select name="product_id" required>
            <option value="">Zgjidh librin</option>
            <?php foreach ($products as $product): ?>
                <option value="<?php echo (int)$product['id']; ?>" <?php echo isset($editCart['product_id']) && (int)$editCart['product_id'] === (int)$product['id'] ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($product['title'] . ' - ' . $product['author']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <input type="number" name="quantity" min="1" value="<?php echo htmlspecialchars((string)($editCart['quantity'] ?? 1)); ?>" required>
        <button class="btn-primary" type="submit"><?php echo $editCart ? 'Ruaj ndryshimet' : 'Shto ne shporte'; ?></button>
    </form>

    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>User</th>
                <th>Libri</th>
                <th>Sasia</th>
                <th>Data shtimit</th>
                <th>Veprime</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($cartRows as $row): ?>
                <tr>
                    <td>#<?php echo (int)$row['id']; ?></td>
                    <td>
                        <strong><?php echo htmlspecialchars($row['full_name']); ?></strong><br>
                        <small><?php echo htmlspecialchars($row['email']); ?></small>
                    </td>
                    <td><?php echo htmlspecialchars($row['title']); ?><br><small><?php echo htmlspecialchars($row['author']); ?></small></td>
                    <td><?php echo (int)$row['quantity']; ?></td>
                    <td><?php echo htmlspecialchars((string)$row['added_at']); ?></td>
                    <td>
                        <a href="/Projekti/admin/cart.php?edit=<?php echo (int)$row['id']; ?>">Edit</a>
                        <a data-confirm="A jeni i sigurt?" href="/Projekti/admin/cart.php?delete=<?php echo (int)$row['id']; ?>">Fshi</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>
<?php include __DIR__ . '/../includes/footer.php'; ?>