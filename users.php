<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/admin-flash.php';

$pageTitle = 'Admin Perdorues';
$pageCss = 'admin.css';
$pageJs = 'admin.js';

if (isset($_POST['action']) && $_POST['action'] === 'save') {
    $id = (int)($_POST['id'] ?? 0);
    $fullName = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($fullName === '' || $email === '') {
        setAdminFlash('Ploteso emrin dhe emailin e perdoruesit.');
        header('Location: /Projekti/admin/users.php');
        exit;
    }

    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? AND id <> ?');
    $stmt->execute([$email, $id]);
    if ($stmt->fetch()) {
        setAdminFlash('Ky email ekziston tashme.');
        header('Location: /Projekti/admin/users.php');
        exit;
    }

    if ($id > 0) {
        if ($password !== '') {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('UPDATE users SET full_name = ?, email = ?, password_hash = ? WHERE id = ?');
            $stmt->execute([$fullName, $email, $passwordHash, $id]);
        } else {
            $stmt = $pdo->prepare('UPDATE users SET full_name = ?, email = ? WHERE id = ?');
            $stmt->execute([$fullName, $email, $id]);
        }
        setAdminFlash('Perdoruesi u ndryshua me sukses.');
    } else {
        if ($password === '') {
            setAdminFlash('Ploteso fjalekalimin per perdoruesin e ri.');
            header('Location: /Projekti/admin/users.php');
            exit;
        }

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('INSERT INTO users (full_name, email, password_hash) VALUES (?, ?, ?)');
        $stmt->execute([$fullName, $email, $passwordHash]);
        setAdminFlash('Perdoruesi u shtua me sukses.');
    }

    header('Location: /Projekti/admin/users.php');
    exit;
}

if (isset($_GET['delete'])) {
    $deleteId = (int)$_GET['delete'];
    $stmt = $pdo->prepare('DELETE FROM users WHERE id = ?');
    $stmt->execute([$deleteId]);
    setAdminFlash('Perdoruesi u fshi me sukses.');
    header('Location: /Projekti/admin/users.php');
    exit;
}

$editUser = null;
if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    $stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
    $stmt->execute([$editId]);
    $editUser = $stmt->fetch();
}

$users = $pdo->query('SELECT id, full_name, email, created_at FROM users ORDER BY created_at DESC')->fetchAll();
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
            <h2 class="section-title">Menaxho perdoruesit</h2>
            <p>Rregullo llogarite e lexuesve dhe informacionet e tyre kryesore.</p>
        </div>
        <a class="btn-secondary" href="/Projekti/admin/dashboard.php">Dashboard</a>
    </div>

    <?php if ($flashSuccess): ?>
        <p class="admin-flash success"><?php echo htmlspecialchars($flashSuccess); ?></p>
    <?php endif; ?>

    <form class="card admin-form" method="post">
        <input type="hidden" name="action" value="save">
        <input type="hidden" name="id" value="<?php echo (int)($editUser['id'] ?? 0); ?>">
        <input type="text" name="full_name" placeholder="Emri i plote" value="<?php echo htmlspecialchars($editUser['full_name'] ?? ''); ?>" required>
        <input type="email" name="email" placeholder="Email" value="<?php echo htmlspecialchars($editUser['email'] ?? ''); ?>" required>
        <input type="password" name="password" placeholder="Fjalekalimi (lene bosh per te mos ndryshuar)">
        <button class="btn-primary" type="submit"><?php echo $editUser ? 'Ruaj ndryshimet' : 'Shto perdorues'; ?></button>
    </form>

    <table class="table">
        <thead>
            <tr>
                <th>Emri</th>
                <th>Email</th>
                <th>Krijuar</th>
                <th>Veprime</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td><?php echo htmlspecialchars((string)$user['created_at']); ?></td>
                    <td>
                        <a href="/Projekti/admin/users.php?edit=<?php echo (int)$user['id']; ?>">Edit</a>
                        <a data-confirm="A jeni i sigurt?" href="/Projekti/admin/users.php?delete=<?php echo (int)$user['id']; ?>">Fshi</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
