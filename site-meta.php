<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/admin-flash.php';

$pageTitle = 'Admin Site Meta';
$pageCss = 'admin.css';
$pageJs = 'admin.js';

if (isset($_POST['action']) && $_POST['action'] === 'save') {
    $metaKey = trim($_POST['meta_key'] ?? '');
    $metaValue = trim($_POST['meta_value'] ?? '');
    $originalKey = trim($_POST['original_key'] ?? '');

    if ($metaKey === '') {
        setAdminFlash('Ploteso meta_key.');
        header('Location: /Projekti/admin/site-meta.php');
        exit;
    }

    if ($originalKey !== '') {
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM site_meta WHERE meta_key = ?');
        $stmt->execute([$metaKey]);
        $exists = (int)$stmt->fetchColumn() > 0;

        if ($metaKey !== $originalKey && $exists) {
            setAdminFlash('Ky meta_key ekziston tashme.');
            header('Location: /Projekti/admin/site-meta.php');
            exit;
        }

        $stmt = $pdo->prepare('UPDATE site_meta SET meta_key = ?, meta_value = ? WHERE meta_key = ?');
        $stmt->execute([$metaKey, $metaValue, $originalKey]);
        setAdminFlash('Meta u ndryshua me sukses.');
    } else {
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM site_meta WHERE meta_key = ?');
        $stmt->execute([$metaKey]);
        if ((int)$stmt->fetchColumn() > 0) {
            setAdminFlash('Ky meta_key ekziston tashme.');
            header('Location: /Projekti/admin/site-meta.php');
            exit;
        }

        $stmt = $pdo->prepare('INSERT INTO site_meta (meta_key, meta_value) VALUES (?, ?)');
        $stmt->execute([$metaKey, $metaValue]);
        setAdminFlash('Meta u shtua me sukses.');
    }

    header('Location: /Projekti/admin/site-meta.php');
    exit;
}

if (isset($_GET['delete'])) {
    $deleteKey = (string)$_GET['delete'];
    $stmt = $pdo->prepare('DELETE FROM site_meta WHERE meta_key = ?');
    $stmt->execute([$deleteKey]);
    setAdminFlash('Meta u fshi me sukses.');
    header('Location: /Projekti/admin/site-meta.php');
    exit;
}

$editMeta = null;
if (isset($_GET['edit'])) {
    $editKey = (string)$_GET['edit'];
    $stmt = $pdo->prepare('SELECT * FROM site_meta WHERE meta_key = ?');
    $stmt->execute([$editKey]);
    $editMeta = $stmt->fetch();
}

$siteMetaRows = $pdo->query('SELECT meta_key, meta_value, updated_at FROM site_meta ORDER BY updated_at DESC')->fetchAll();
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
            <h2 class="section-title">Menaxho site meta</h2>
            <p>Ruaj konfigurime dhe shenime te vogla per platformen.</p>
        </div>
        <a class="btn-secondary" href="/Projekti/admin/dashboard.php">Dashboard</a>
    </div>

    <?php if ($flashSuccess): ?>
        <p class="admin-flash success"><?php echo htmlspecialchars($flashSuccess); ?></p>
    <?php endif; ?>

    <form class="card admin-form" method="post">
        <input type="hidden" name="action" value="save">
        <input type="hidden" name="original_key" value="<?php echo htmlspecialchars($editMeta['meta_key'] ?? ''); ?>">
        <input type="text" name="meta_key" placeholder="meta_key" value="<?php echo htmlspecialchars($editMeta['meta_key'] ?? ''); ?>" required>
        <textarea name="meta_value" placeholder="meta_value" required><?php echo htmlspecialchars($editMeta['meta_value'] ?? ''); ?></textarea>
        <button class="btn-primary" type="submit"><?php echo $editMeta ? 'Ruaj ndryshimet' : 'Shto meta'; ?></button>
    </form>

    <table class="table">
        <thead>
            <tr>
                <th>Key</th>
                <th>Value</th>
                <th>Updated</th>
                <th>Veprime</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($siteMetaRows as $row): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['meta_key']); ?></td>
                    <td><?php echo htmlspecialchars($row['meta_value']); ?></td>
                    <td><?php echo htmlspecialchars((string)$row['updated_at']); ?></td>
                    <td>
                        <a href="/Projekti/admin/site-meta.php?edit=<?php echo urlencode($row['meta_key']); ?>">Edit</a>
                        <a data-confirm="A jeni i sigurt?" href="/Projekti/admin/site-meta.php?delete=<?php echo urlencode($row['meta_key']); ?>">Fshi</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
