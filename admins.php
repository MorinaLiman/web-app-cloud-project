<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/admin-flash.php';

$pageTitle = 'Admin Administratore';
$pageCss = 'admin.css';
$pageJs = 'admin.js';

function getAdminPasswordColumn(PDO $pdo): string
{
    static $column = null;

    if ($column !== null) {
        return $column;
    }

    $columns = ['password', 'password_hash'];

    foreach ($columns as $candidate) {
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = ? AND column_name = ?');
        $stmt->execute(['admins', $candidate]);

        if ((int)$stmt->fetchColumn() > 0) {
            $column = $candidate;
            return $column;
        }
    }

    $column = 'password';
    return $column;
}

$adminPasswordColumn = getAdminPasswordColumn($pdo);

if (isset($_POST['action']) && $_POST['action'] === 'save') {
    $id = (int)($_POST['id'] ?? 0);
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username === '') {
        setAdminFlash('Ploteso username-in e adminit.');
        header('Location: /Projekti/admin/admins.php');
        exit;
    }

    if ($id > 0) {
        if ($password !== '') {
            $stmt = $pdo->prepare(sprintf('UPDATE admins SET username = ?, `%s` = ? WHERE id = ?', $adminPasswordColumn));
            $stmt->execute([$username, $password, $id]);
        } else {
            $stmt = $pdo->prepare('UPDATE admins SET username = ? WHERE id = ?');
            $stmt->execute([$username, $id]);
        }
        setAdminFlash('Admini u ndryshua me sukses.');
    } else {
        if ($password === '') {
            setAdminFlash('Ploteso edhe fjalekalimin per adminin e ri.');
            header('Location: /Projekti/admin/admins.php');
            exit;
        }

        $stmt = $pdo->prepare(sprintf('INSERT INTO admins (username, `%s`) VALUES (?, ?)', $adminPasswordColumn));
        $stmt->execute([$username, $password]);
        setAdminFlash('Admini u shtua me sukses.');
    }

    header('Location: /Projekti/admin/admins.php');
    exit;
}

if (isset($_GET['delete'])) {
    $deleteId = (int)$_GET['delete'];
    $adminCount = (int)$pdo->query('SELECT COUNT(*) FROM admins')->fetchColumn();

    if ($adminCount <= 1) {
        setAdminFlash('Nuk mund te fshish adminin e fundit.');
    } else {
        $stmt = $pdo->prepare('DELETE FROM admins WHERE id = ?');
        $stmt->execute([$deleteId]);
        setAdminFlash('Admini u fshi me sukses.');
    }

    header('Location: /Projekti/admin/admins.php');
    exit;
}

$editAdmin = null;
if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    $stmt = $pdo->prepare('SELECT * FROM admins WHERE id = ?');
    $stmt->execute([$editId]);
    $editAdmin = $stmt->fetch();
}

$admins = $pdo->query('SELECT id, username, created_at FROM admins ORDER BY created_at DESC')->fetchAll();
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
            <h2 class="section-title">Menaxho administratoret</h2>
            <p>Krijo ose ndrysho llogari admini per menaxhim te panelit.</p>
        </div>
        <a class="btn-secondary" href="/Projekti/admin/dashboard.php">Dashboard</a>
    </div>

    <?php if ($flashSuccess): ?>
        <p class="admin-flash success"><?php echo htmlspecialchars($flashSuccess); ?></p>
    <?php endif; ?>

    <form class="card admin-form" method="post">
        <input type="hidden" name="action" value="save">
        <input type="hidden" name="id" value="<?php echo (int)($editAdmin['id'] ?? 0); ?>">
        <input type="text" name="username" placeholder="Username" value="<?php echo htmlspecialchars($editAdmin['username'] ?? ''); ?>" required>
        <input type="password" name="password" placeholder="Fjalekalimi (lene bosh per te mos ndryshuar)">
        <button class="btn-primary" type="submit"><?php echo $editAdmin ? 'Ruaj ndryshimet' : 'Shto admin'; ?></button>
    </form>

    <table class="table">
        <thead>
            <tr>
                <th>Username</th>
                <th>Krijuar</th>
                <th>Veprime</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($admins as $admin): ?>
                <tr>
                    <td><?php echo htmlspecialchars($admin['username']); ?></td>
                    <td><?php echo htmlspecialchars((string)$admin['created_at']); ?></td>
                    <td>
                        <a href="/Projekti/admin/admins.php?edit=<?php echo (int)$admin['id']; ?>">Edit</a>
                        <a data-confirm="A jeni i sigurt?" href="/Projekti/admin/admins.php?delete=<?php echo (int)$admin['id']; ?>">Fshi</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
