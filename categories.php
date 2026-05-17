<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/admin-flash.php';

$pageTitle = 'Admin Kategori';
$pageCss = 'admin.css';
$pageJs = 'admin.js';

if (isset($_POST['action']) && $_POST['action'] === 'save') {
    $id = (int)($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? '');

    if ($id > 0) {
        $stmt = $pdo->prepare('UPDATE categories SET name = ? WHERE id = ?');
        $stmt->execute([$name, $id]);
        setAdminFlash('Kategoria u ndryshua me sukses.');
    } else {
        $stmt = $pdo->prepare('INSERT INTO categories (name) VALUES (?)');
        $stmt->execute([$name]);
        setAdminFlash('Kategoria u shtua me sukses.');
    }
    header('Location: /Projekti/admin/categories.php');
    exit;
}

if (isset($_GET['delete'])) {
    $deleteId = (int)$_GET['delete'];
    $stmt = $pdo->prepare('DELETE FROM categories WHERE id = ?');
    $stmt->execute([$deleteId]);
    setAdminFlash('Kategoria u fshi me sukses.');
    header('Location: /Projekti/admin/categories.php');
    exit;
}

$editCategory = null;
if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    $stmt = $pdo->prepare('SELECT * FROM categories WHERE id = ?');
    $stmt->execute([$editId]);
    $editCategory = $stmt->fetch();
}

$categories = $pdo->query('SELECT * FROM categories ORDER BY name')->fetchAll();
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
            <h2 class="section-title">Menaxho kategorite</h2>
            <p>Rregullo strukturen e katalogut me kategori te qarta dhe te organizuara.</p>
        </div>
        <a class="btn-secondary" href="/Projekti/admin/dashboard.php">Dashboard</a>
    </div>

    <?php if ($flashSuccess): ?>
        <p class="admin-flash success"><?php echo htmlspecialchars($flashSuccess); ?></p>
    <?php endif; ?>

    <form class="card admin-form" method="post">
        <input type="hidden" name="action" value="save">
        <input type="hidden" name="id" value="<?php echo $editCategory['id'] ?? 0; ?>">
        <input type="text" name="name" placeholder="Emri i kategorise" value="<?php echo htmlspecialchars($editCategory['name'] ?? ''); ?>" required>
        <button class="btn-primary" type="submit"><?php echo $editCategory ? 'Ndrysho' : 'Shto'; ?></button>
    </form>

    <table class="table">
        <thead>
            <tr>
                <th>Kategoria</th>
                <th>Veprime</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($categories as $category): ?>
                <tr>
                    <td><?php echo htmlspecialchars($category['name']); ?></td>
                    <td>
                        <a href="/Projekti/admin/categories.php?edit=<?php echo (int)$category['id']; ?>">Edit</a>
                        <a data-confirm="A jeni i sigurt?" href="/Projekti/admin/categories.php?delete=<?php echo (int)$category['id']; ?>">Fshi</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
