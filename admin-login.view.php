<?php include __DIR__ . '/../includes/header.php'; ?>
<section class="container auth-shell" data-reveal>
    <div class="auth-panel admin-panel">
        <div class="auth-brand">
            <span class="logo-mark"><i class="fa fa-shield" aria-hidden="true"></i></span>
            <div>
                <strong>LibraryStore</strong>
                <span>Admin Panel</span>
            </div>
        </div>
        <h1 class="auth-heading">Admin Login</h1>
        <p class="auth-subtitle">Qasje e sigurt per menaxhimin e katalogut dhe porosive.</p>
        <?php if ($errorAdmin): ?>
            <p class="login-error"><?php echo htmlspecialchars($errorAdmin); ?></p>
        <?php endif; ?>
        <form class="auth-form" method="post">
            <div class="input-group">
                <span class="input-icon"><i class="fa fa-user" aria-hidden="true"></i></span>
                <input type="text" name="username" placeholder="Username" required>
            </div>
            <div class="input-group">
                <span class="input-icon"><i class="fa fa-lock" aria-hidden="true"></i></span>
                <input type="password" name="password" placeholder="Password" required>
            </div>
            <button class="btn-primary" type="submit">Hyr si Admin</button>
        </form>
        <div class="auth-links">
            <a href="/Projekti/user-login.php">Klienti? Logohu ketu</a>
            <span class="link-sep">|</span>
            <a href="/Projekti/register.php">Regjistrohu</a>
        </div>
    </div>
</section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
