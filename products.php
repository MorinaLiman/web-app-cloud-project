<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/admin-flash.php';

$pageTitle = 'Admin Produkte';
$pageCss = 'admin.css';
$pageJs = 'admin.js';

if (isset($_POST['action']) && $_POST['action'] === 'save') {
    $id = (int)($_POST['id'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    $author = trim($_POST['author'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $year = (int)($_POST['year_published'] ?? 0);
    $isbn = trim($_POST['isbn'] ?? '');
    $imageUrl = trim($_POST['image_url'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $categoryId = (int)($_POST['category_id'] ?? 0);

    if ($id > 0) {
        $stmt = $pdo->prepare('UPDATE products SET title = ?, author = ?, price = ?, year_published = ?, isbn = ?, image_url = ?, description = ?, category_id = ? WHERE id = ?');
        $stmt->execute([$title, $author, $price, $year, $isbn, $imageUrl, $description, $categoryId, $id]);
        setAdminFlash('Produkti u ndryshua me sukses.');
    } else {
        setAdminFlash('Zgjidh nje produkt nga tabela poshte per ta ndryshuar.');
    }
    header('Location: /Projekti/admin/products.php');
    exit;
}

if (isset($_POST['action']) && $_POST['action'] === 'insert') {
    $title = trim($_POST['title'] ?? '');
    $author = trim($_POST['author'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $year = (int)($_POST['year_published'] ?? 0);
    $isbn = trim($_POST['isbn'] ?? '');
    $imageUrl = trim($_POST['image_url'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $categoryId = (int)($_POST['category_id'] ?? 0);

    if ($title !== '' && $author !== '' && $price > 0 && $year > 0 && $isbn !== '' && $imageUrl !== '' && $description !== '' && $categoryId > 0) {
        $stmt = $pdo->prepare('INSERT INTO products (title, author, price, year_published, isbn, image_url, description, category_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([$title, $author, $price, $year, $isbn, $imageUrl, $description, $categoryId]);
        setAdminFlash('Produkti u shtua me sukses.');
    } else {
        setAdminFlash('Ploteso te gjitha fushat e produktit.');
    }

    header('Location: /Projekti/admin/products.php');
    exit;
}

if (isset($_GET['delete'])) {
    $deleteId = (int)$_GET['delete'];
    $stmt = $pdo->prepare('DELETE FROM products WHERE id = ?');
    $stmt->execute([$deleteId]);
    setAdminFlash('Produkti u fshi me sukses.');
    header('Location: /Projekti/admin/products.php');
    exit;
}

$editProduct = null;
if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    $stmt = $pdo->prepare('SELECT * FROM products WHERE id = ?');
    $stmt->execute([$editId]);
    $editProduct = $stmt->fetch();
}

$products = $pdo->query('SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.created_at DESC')->fetchAll();
$categories = $pdo->query('SELECT id, name FROM categories ORDER BY name')->fetchAll();
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
            <h2 class="section-title">Menaxho produktet</h2>
            <p>Shto, ndrysho ose fshi libra nga katalogu i platformes.</p>
        </div>
        <a class="btn-secondary" href="/Projekti/admin/dashboard.php">Dashboard</a>
    </div>

    <?php if ($flashSuccess): ?>
        <p class="admin-flash success"><?php echo htmlspecialchars($flashSuccess); ?></p>
    <?php endif; ?>

    <div class="grid admin-dual-grid">
        <form class="card admin-form admin-insert-card" method="post">
            <input type="hidden" name="action" value="insert">
            <h3>Shto liber te ri</h3>
            <input type="text" name="title" placeholder="Titulli" required>
            <input type="text" name="author" placeholder="Autori" required>
            <input type="number" step="0.01" name="price" placeholder="Cmimi" required>
            <input type="number" name="year_published" placeholder="Viti" required>
            <input type="text" name="isbn" placeholder="ISBN" required>
            <input type="text" name="image_url" placeholder="Image URL" required>
            <textarea name="description" placeholder="Pershkrimi" required></textarea>
            <select name="category_id" required>
                <option value="">Zgjidh kategorine</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?php echo (int)$category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
                <?php endforeach; ?>
            </select>
            <button class="btn-primary" type="submit">Shto librin</button>
        </form>

        <div class="card admin-form admin-edit-card">
            <h3>Ndrysho liber ekzistues</h3>
            <?php if ($editProduct): ?>
                <form method="post" class="admin-form-inner">
                    <input type="hidden" name="action" value="save">
                    <input type="hidden" name="id" value="<?php echo $editProduct['id'] ?? 0; ?>">
                    <input type="text" name="title" placeholder="Titulli" value="<?php echo htmlspecialchars($editProduct['title'] ?? ''); ?>" required>
                    <input type="text" name="author" placeholder="Autori" value="<?php echo htmlspecialchars($editProduct['author'] ?? ''); ?>" required>
                    <input type="number" step="0.01" name="price" placeholder="Cmimi" value="<?php echo htmlspecialchars($editProduct['price'] ?? ''); ?>" required>
                    <input type="number" name="year_published" placeholder="Viti" value="<?php echo htmlspecialchars($editProduct['year_published'] ?? ''); ?>" required>
                    <input type="text" name="isbn" placeholder="ISBN" value="<?php echo htmlspecialchars($editProduct['isbn'] ?? ''); ?>" required>
                    <input type="text" name="image_url" placeholder="Image URL" value="<?php echo htmlspecialchars($editProduct['image_url'] ?? ''); ?>" required>
                    <textarea name="description" placeholder="Pershkrimi" required><?php echo htmlspecialchars($editProduct['description'] ?? ''); ?></textarea>
                    <select name="category_id" required>
                        <option value="">Zgjidh kategorine</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo (int)$category['id']; ?>" <?php echo isset($editProduct['category_id']) && (int)$editProduct['category_id'] === (int)$category['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($category['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button class="btn-primary" type="submit">Ruaj ndryshimet</button>
                </form>
            <?php else: ?>
                <p>Zgjidh nje liber nga tabela poshte per ta ndryshuar.</p>
            <?php endif; ?>
        </div>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Libri</th>
                <th>Autori</th>
                <th>Kategoria</th>
                <th>Cmimi</th>
                <th>Veprime</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $product): ?>
                <tr>
                    <td><?php echo htmlspecialchars($product['title']); ?></td>
                    <td><?php echo htmlspecialchars($product['author']); ?></td>
                    <td><?php echo htmlspecialchars($product['category_name']); ?></td>
                    <td><?php echo number_format($product['price'], 2); ?> EUR</td>
                    <td>
                        <a href="/Projekti/admin/products.php?edit=<?php echo (int)$product['id']; ?>">Edit</a>
                        <a data-confirm="A jeni i sigurt?" href="/Projekti/admin/products.php?delete=<?php echo (int)$product['id']; ?>">Fshi</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
