<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/admin-flash.php';

$pageTitle = 'Admin Porosi';
$pageCss = 'admin.css';
$pageJs = 'admin.js';

if (isset($_POST['action']) && $_POST['action'] === 'save') {
    $id = (int)($_POST['id'] ?? 0);
    $userId = (int)($_POST['user_id'] ?? 0);
    $buyerFullName = trim($_POST['buyer_full_name'] ?? '');
    $buyerFirstName = trim($_POST['buyer_first_name'] ?? '');
    $buyerLastName = trim($_POST['buyer_last_name'] ?? '');
    $buyerEmail = trim($_POST['buyer_account_email'] ?? '');
    $totalPrice = (float)($_POST['total_price'] ?? 0);
    $status = trim($_POST['status'] ?? 'pending');
    $paymentDate = trim($_POST['payment_date'] ?? '');
    $paymentTime = trim($_POST['payment_time'] ?? '');
    $paymentMethod = trim($_POST['payment_method'] ?? 'credit_card');
    $paymentCardholderName = trim($_POST['payment_cardholder_name'] ?? '');
    $paymentCardLast4 = substr(preg_replace('/\D+/', '', $_POST['payment_card_last4'] ?? ''), -4);
    $paymentStatus = trim($_POST['payment_status'] ?? 'pending');

    if ($id > 0) {
        $stmt = $pdo->prepare('UPDATE orders SET user_id = ?, buyer_full_name = ?, buyer_first_name = ?, buyer_last_name = ?, buyer_account_email = ?, total_price = ?, status = ?, payment_date = ?, payment_time = ?, payment_method = ?, payment_cardholder_name = ?, payment_card_last4 = ?, payment_status = ? WHERE id = ?');
        $stmt->execute([$userId, $buyerFullName, $buyerFirstName, $buyerLastName, $buyerEmail, $totalPrice, $status, $paymentDate ?: null, $paymentTime ?: null, $paymentMethod, $paymentCardholderName ?: null, $paymentCardLast4 ?: null, $paymentStatus, $id]);
        setAdminFlash('Porosia u ndryshua me sukses.');
    } else {
        setAdminFlash('Zgjidh nje porosi nga tabela poshte per ta ndryshuar.');
    }

    header('Location: /Projekti/admin/orders.php');
    exit;
}

if (isset($_POST['action']) && $_POST['action'] === 'insert') {
    $userId = (int)($_POST['user_id'] ?? 0);
    $buyerFullName = trim($_POST['buyer_full_name'] ?? '');
    $buyerFirstName = trim($_POST['buyer_first_name'] ?? '');
    $buyerLastName = trim($_POST['buyer_last_name'] ?? '');
    $buyerEmail = trim($_POST['buyer_account_email'] ?? '');
    $totalPrice = (float)($_POST['total_price'] ?? 0);
    $status = trim($_POST['status'] ?? 'pending');
    $paymentDate = trim($_POST['payment_date'] ?? '');
    $paymentTime = trim($_POST['payment_time'] ?? '');
    $paymentMethod = trim($_POST['payment_method'] ?? 'credit_card');
    $paymentCardholderName = trim($_POST['payment_cardholder_name'] ?? '');
    $paymentCardLast4 = substr(preg_replace('/\D+/', '', $_POST['payment_card_last4'] ?? ''), -4);
    $paymentStatus = trim($_POST['payment_status'] ?? 'pending');

    if ($userId <= 0 || $buyerFullName === '' || $buyerFirstName === '' || $buyerLastName === '' || $buyerEmail === '' || $totalPrice <= 0) {
        setAdminFlash('Ploteso te gjitha fushat e detyrueshme per porosine.');
    } else {
        $stmt = $pdo->prepare('INSERT INTO orders (user_id, buyer_full_name, buyer_first_name, buyer_last_name, buyer_account_email, total_price, status, payment_date, payment_time, payment_method, payment_cardholder_name, payment_card_last4, payment_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([$userId, $buyerFullName, $buyerFirstName, $buyerLastName, $buyerEmail, $totalPrice, $status, $paymentDate ?: null, $paymentTime ?: null, $paymentMethod, $paymentCardholderName ?: null, $paymentCardLast4 ?: null, $paymentStatus]);
        setAdminFlash('Porosia u shtua me sukses.');
    }

    header('Location: /Projekti/admin/orders.php');
    exit;
}

if (isset($_GET['delete'])) {
    $deleteId = (int)$_GET['delete'];
    $stmt = $pdo->prepare('DELETE FROM orders WHERE id = ?');
    $stmt->execute([$deleteId]);
    setAdminFlash('Porosia u fshi me sukses.');
    header('Location: /Projekti/admin/orders.php');
    exit;
}

$editOrder = null;
if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    $stmt = $pdo->prepare('SELECT * FROM orders WHERE id = ?');
    $stmt->execute([$editId]);
    $editOrder = $stmt->fetch();
}

$users = $pdo->query('SELECT id, full_name, email FROM users ORDER BY full_name')->fetchAll();
$orders = $pdo->query(
    'SELECT o.*, 
        (SELECT COUNT(*) FROM order_items oi WHERE oi.order_id = o.id) AS item_count,
        (SELECT GROUP_CONCAT(CONCAT(p.title, " x", oi.quantity) SEPARATOR ", ")
         FROM order_items oi
         JOIN products p ON p.id = oi.product_id
         WHERE oi.order_id = o.id) AS books
     FROM orders o
     ORDER BY o.created_at DESC'
)->fetchAll();

$orderItems = $pdo->query(
    'SELECT oi.id, oi.order_id, oi.quantity, oi.price, p.title, p.author
     FROM order_items oi
     JOIN products p ON p.id = oi.product_id
     ORDER BY oi.order_id DESC, oi.id ASC'
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
            <h2 class="section-title">Menaxho porosite</h2>
            <p>Rishiko blerjet, statuset dhe detajet e pagesave nga nje panel i vetem.</p>
        </div>
        <a class="btn-secondary" href="/Projekti/admin/dashboard.php">Dashboard</a>
    </div>

    <?php if ($flashSuccess): ?>
        <p class="admin-flash success"><?php echo htmlspecialchars($flashSuccess); ?></p>
    <?php endif; ?>

    <div class="card" style="margin-bottom:24px; padding:18px; border-radius:18px; background:rgba(47,107,255,0.06);">
        <strong>Shenim:</strong> Porosite krijohen automatikisht nga checkout-i i shportes. Ketu mund t'i ndryshosh ose t'i fshish pa e prishur strukturën e databazes.
    </div>

    <div class="grid admin-dual-grid">
        <form class="card admin-form admin-insert-card" method="post">
            <input type="hidden" name="action" value="insert">
            <h3>Shto porosi te re</h3>
            <select name="user_id" required>
                <option value="">Zgjidh userin</option>
                <?php foreach ($users as $user): ?>
                    <option value="<?php echo (int)$user['id']; ?>">
                        <?php echo htmlspecialchars($user['full_name'] . ' (' . $user['email'] . ')'); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <input type="text" name="buyer_full_name" placeholder="Emri i plote" required>
            <input type="text" name="buyer_first_name" placeholder="Emri" required>
            <input type="text" name="buyer_last_name" placeholder="Mbiemri" required>
            <input type="email" name="buyer_account_email" placeholder="Email" required>
            <input type="number" step="0.01" name="total_price" placeholder="Totali" required>
            <select name="status">
                <option value="pending">pending</option>
                <option value="completed">completed</option>
                <option value="cancelled">cancelled</option>
            </select>
            <input type="date" name="payment_date">
            <input type="time" name="payment_time">
            <select name="payment_method">
                <option value="credit_card">credit_card</option>
                <option value="debit_card">debit_card</option>
            </select>
            <input type="text" name="payment_cardholder_name" placeholder="Emri ne karte">
            <input type="text" name="payment_card_last4" placeholder="Last 4">
            <select name="payment_status">
                <option value="pending">pending</option>
                <option value="paid">paid</option>
                <option value="failed">failed</option>
            </select>
            <button class="btn-primary" type="submit">Shto porosine</button>
        </form>

        <div class="card admin-form admin-edit-card">
            <h3>Ndrysho porosi ekzistuese</h3>
            <?php if ($editOrder): ?>
                <form method="post" class="admin-form-inner">
                    <input type="hidden" name="action" value="save">
                    <input type="hidden" name="id" value="<?php echo (int)$editOrder['id']; ?>">
                    <select name="user_id" required>
                        <option value="">Zgjidh userin</option>
                        <?php foreach ($users as $user): ?>
                            <option value="<?php echo (int)$user['id']; ?>" <?php echo (int)$editOrder['user_id'] === (int)$user['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($user['full_name'] . ' (' . $user['email'] . ')'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <input type="text" name="buyer_full_name" placeholder="Emri i plote" value="<?php echo htmlspecialchars($editOrder['buyer_full_name']); ?>" required>
                    <input type="text" name="buyer_first_name" placeholder="Emri" value="<?php echo htmlspecialchars($editOrder['buyer_first_name']); ?>" required>
                    <input type="text" name="buyer_last_name" placeholder="Mbiemri" value="<?php echo htmlspecialchars($editOrder['buyer_last_name']); ?>" required>
                    <input type="email" name="buyer_account_email" placeholder="Email" value="<?php echo htmlspecialchars($editOrder['buyer_account_email']); ?>" required>
                    <input type="number" step="0.01" name="total_price" placeholder="Totali" value="<?php echo htmlspecialchars($editOrder['total_price']); ?>" required>
                    <select name="status">
                        <option value="pending" <?php echo $editOrder['status'] === 'pending' ? 'selected' : ''; ?>>pending</option>
                        <option value="completed" <?php echo $editOrder['status'] === 'completed' ? 'selected' : ''; ?>>completed</option>
                        <option value="cancelled" <?php echo $editOrder['status'] === 'cancelled' ? 'selected' : ''; ?>>cancelled</option>
                    </select>
                    <input type="date" name="payment_date" value="<?php echo htmlspecialchars((string)$editOrder['payment_date']); ?>">
                    <input type="time" name="payment_time" value="<?php echo htmlspecialchars(substr((string)$editOrder['payment_time'], 0, 5)); ?>">
                    <select name="payment_method">
                        <option value="credit_card" <?php echo $editOrder['payment_method'] === 'credit_card' ? 'selected' : ''; ?>>credit_card</option>
                        <option value="debit_card" <?php echo $editOrder['payment_method'] === 'debit_card' ? 'selected' : ''; ?>>debit_card</option>
                    </select>
                    <input type="text" name="payment_cardholder_name" placeholder="Emri ne karte" value="<?php echo htmlspecialchars((string)$editOrder['payment_cardholder_name']); ?>">
                    <input type="text" name="payment_card_last4" placeholder="Last 4" value="<?php echo htmlspecialchars((string)$editOrder['payment_card_last4']); ?>">
                    <select name="payment_status">
                        <option value="pending" <?php echo $editOrder['payment_status'] === 'pending' ? 'selected' : ''; ?>>pending</option>
                        <option value="paid" <?php echo $editOrder['payment_status'] === 'paid' ? 'selected' : ''; ?>>paid</option>
                        <option value="failed" <?php echo $editOrder['payment_status'] === 'failed' ? 'selected' : ''; ?>>failed</option>
                    </select>
                    <button class="btn-primary" type="submit">Ruaj ndryshimet</button>
                </form>
            <?php else: ?>
                <p>Zgjidh nje porosi nga tabela poshte per ta ndryshuar.</p>
            <?php endif; ?>
        </div>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Porosia</th>
                <th>Klienti</th>
                <th>Libra</th>
                <th>Data / Ora</th>
                <th>Totali</th>
                <th>Veprime</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $order): ?>
                <tr>
                    <td>#<?php echo (int)$order['id']; ?><br><small><?php echo htmlspecialchars($order['status']); ?> / <?php echo htmlspecialchars($order['payment_status']); ?></small></td>
                    <td>
                        <?php echo htmlspecialchars($order['buyer_full_name']); ?><br>
                        <small><?php echo htmlspecialchars($order['buyer_account_email']); ?></small>
                    </td>
                    <td>
                        <?php echo (int)$order['item_count']; ?> artikuj<br>
                        <small><?php echo htmlspecialchars((string)($order['books'] ?? '')); ?></small>
                    </td>
                    <td>
                        <?php echo htmlspecialchars((string)($order['payment_date'] ?? $order['created_at'])); ?><br>
                        <small><?php echo htmlspecialchars(substr((string)($order['payment_time'] ?? $order['created_at']), 0, 5)); ?></small>
                    </td>
                    <td><?php echo number_format((float)$order['total_price'], 2); ?> EUR</td>
                    <td>
                        <a href="/Projekti/admin/orders.php?edit=<?php echo (int)$order['id']; ?>">Edit</a>
                        <a data-confirm="A jeni i sigurt?" href="/Projekti/admin/orders.php?delete=<?php echo (int)$order['id']; ?>">Fshi</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="admin-header" style="margin-top:32px;">
        <h2 class="section-title">Detajet e librave ne porosi</h2>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Libri</th>
                <th>Autori</th>
                <th>Sasia</th>
                <th>Cmimi</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orderItems as $item): ?>
                <tr>
                    <td>#<?php echo (int)$item['order_id']; ?></td>
                    <td><?php echo htmlspecialchars($item['title']); ?></td>
                    <td><?php echo htmlspecialchars($item['author']); ?></td>
                    <td><?php echo (int)$item['quantity']; ?></td>
                    <td><?php echo number_format((float)$item['price'], 2); ?> EUR</td>
                    <td><?php echo number_format((float)$item['price'] * (int)$item['quantity'], 2); ?> EUR</td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>
<?php include __DIR__ . '/../includes/footer.php'; ?>