<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/admin-flash.php';

$pageTitle = 'Admin Mesazhe';
$pageCss = 'admin.css';
$pageJs = 'admin.js';

if (isset($_POST['action']) && $_POST['action'] === 'insert') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($name === '' || $email === '' || $subject === '' || $message === '') {
        setAdminFlash('Ploteso te gjitha fushat e mesazhit.');
    } else {
        $stmt = $pdo->prepare('INSERT INTO messages (name, email, subject, message) VALUES (?, ?, ?, ?)');
        $stmt->execute([$name, $email, $subject, $message]);
        setAdminFlash('Mesazhi u shtua me sukses.');
    }

    header('Location: /Projekti/admin/messages.php');
    exit;
}

if (isset($_POST['action']) && $_POST['action'] === 'save') {
    $id = (int)($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($id > 0) {
        $stmt = $pdo->prepare('UPDATE messages SET name = ?, email = ?, subject = ?, message = ? WHERE id = ?');
        $stmt->execute([$name, $email, $subject, $message, $id]);
        setAdminFlash('Mesazhi u ndryshua me sukses.');
    } else {
        setAdminFlash('Zgjidh nje mesazh nga tabela poshte per ta ndryshuar.');
    }

    header('Location: /Projekti/admin/messages.php');
    exit;
}

if (isset($_GET['delete'])) {
    $deleteId = (int)$_GET['delete'];
    $stmt = $pdo->prepare('DELETE FROM messages WHERE id = ?');
    $stmt->execute([$deleteId]);
    setAdminFlash('Mesazhi u fshi me sukses.');
    header('Location: /Projekti/admin/messages.php');
    exit;
}

$editMessage = null;
if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    $stmt = $pdo->prepare('SELECT * FROM messages WHERE id = ?');
    $stmt->execute([$editId]);
    $editMessage = $stmt->fetch();
}

$messages = $pdo->query('SELECT * FROM messages ORDER BY created_at DESC')->fetchAll();
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
            <h2 class="section-title">Mesazhet e lexuesve</h2>
            <p>Shiko dhe menaxho mesazhet qe vijnë nga lexuesit e LibraryStore.</p>
        </div>
        <a class="btn-secondary" href="/Projekti/admin/dashboard.php">Dashboard</a>
    </div>

    <?php if ($flashSuccess): ?>
        <p class="admin-flash success"><?php echo htmlspecialchars($flashSuccess); ?></p>
    <?php endif; ?>

    <div class="grid admin-dual-grid">
        <form class="card admin-form admin-insert-card" method="post">
            <input type="hidden" name="action" value="insert">
            <h3>Shto mesazh te ri</h3>
            <input type="text" name="name" placeholder="Emri" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="text" name="subject" placeholder="Subjekti" required>
            <textarea name="message" placeholder="Mesazhi" required></textarea>
            <button class="btn-primary" type="submit">Shto mesazhin</button>
        </form>

        <div class="card admin-form admin-edit-card">
            <h3>Ndrysho mesazh ekzistues</h3>
            <?php if ($editMessage): ?>
                <form method="post" class="admin-form-inner">
                    <input type="hidden" name="action" value="save">
                    <input type="hidden" name="id" value="<?php echo (int)$editMessage['id']; ?>">
                    <input type="text" name="name" placeholder="Emri" value="<?php echo htmlspecialchars($editMessage['name']); ?>" required>
                    <input type="email" name="email" placeholder="Email" value="<?php echo htmlspecialchars($editMessage['email']); ?>" required>
                    <input type="text" name="subject" placeholder="Subjekti" value="<?php echo htmlspecialchars($editMessage['subject']); ?>" required>
                    <textarea name="message" placeholder="Mesazhi" required><?php echo htmlspecialchars($editMessage['message']); ?></textarea>
                    <button class="btn-primary" type="submit">Ruaj ndryshimet</button>
                </form>
            <?php else: ?>
                <p>Zgjidh nje mesazh nga tabela poshte per ta ndryshuar.</p>
            <?php endif; ?>
        </div>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Emri</th>
                <th>Email</th>
                <th>Subjekti</th>
                <th>Mesazhi</th>
                <th>Veprime</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($messages as $message): ?>
                <tr>
                    <td><?php echo htmlspecialchars($message['name']); ?></td>
                    <td><?php echo htmlspecialchars($message['email']); ?></td>
                    <td><?php echo htmlspecialchars($message['subject']); ?></td>
                    <td><?php echo htmlspecialchars($message['message']); ?></td>
                    <td>
                        <a href="/Projekti/admin/messages.php?edit=<?php echo (int)$message['id']; ?>">Edit</a>
                        <a data-confirm="A jeni i sigurt?" href="/Projekti/admin/messages.php?delete=<?php echo (int)$message['id']; ?>">Fshi</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
