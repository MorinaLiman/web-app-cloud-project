<?php
if (!isset($pageTitle)) {
    $pageTitle = 'BookStore';
}
if (!isset($activePage)) {
    $activePage = '';
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$isLoggedIn = isset($_SESSION['user_logged_in']) || isset($_SESSION['admin_logged_in']);
?>
<!DOCTYPE html>
<html lang="sq">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@300;400;700&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="/Projekti/css/global.css">
    <link rel="stylesheet" href="/Projekti/css/header.css">
    <link rel="stylesheet" href="/Projekti/css/footer.css">
    <?php if (isset($pageCss)): ?>
        <link rel="stylesheet" href="/Projekti/css/<?php echo htmlspecialchars($pageCss); ?>">
    <?php endif; ?>
    <?php if (isset($extraPageCss)): ?>
        <link rel="stylesheet" href="/Projekti/css/<?php echo htmlspecialchars($extraPageCss); ?>">
    <?php endif; ?>
</head>
<body>
<header class="site-header">
    <div class="container header-inner">
        <a class="logo" href="/Projekti/index.php" aria-label="LibraryStore">
            <span class="logo-mark"><i class="fa fa-book" aria-hidden="true"></i></span>
            <span class="logo-text">
                <span class="logo-title">LibraryStore</span>
                <span class="logo-subtitle">Online Bookshop</span>
            </span>
        </a>
        <?php if ($isLoggedIn): ?>
            <nav class="main-nav" id="mainNav">
                <a class="<?php echo $activePage === 'home' ? 'active' : ''; ?>" href="/Projekti/index.php">Shtepi</a>
                <a class="<?php echo $activePage === 'catalog' ? 'active' : ''; ?>" href="/Projekti/catalog.php">Katalogu</a>
                <a class="<?php echo $activePage === 'about' ? 'active' : ''; ?>" href="/Projekti/about.php">Rreth nesh</a>
                <a class="<?php echo $activePage === 'contact' ? 'active' : ''; ?>" href="/Projekti/contact.php">Kontakt</a>
                <a class="<?php echo $activePage === 'blog' ? 'active' : ''; ?>" href="/Projekti/blog.php">Blog</a>
                <a class="<?php echo $activePage === 'xml' ? 'active' : ''; ?>" href="/Projekti/xml.php">XML</a>
                <a class="<?php echo $activePage === 'terms' ? 'active' : ''; ?>" href="/Projekti/terms.php">Kushtet</a>
                <a class="<?php echo $activePage === 'privacy' ? 'active' : ''; ?>" href="/Projekti/privacy.php">Privatesia</a>
                <?php if (isset($_SESSION['admin_logged_in'])): ?>
                    <a class="<?php echo $activePage === 'admin' ? 'active' : ''; ?>" href="/Projekti/admin/dashboard.php">Admin</a>
                    <a class="nav-action nav-action-logout" href="/Projekti/admin/dashboard.php?logout=1"><i class="fa fa-sign-out" aria-hidden="true"></i><span>Logout</span></a>
                <?php else: ?>
                    <a class="<?php echo $activePage === 'cart' ? 'active' : ''; ?>" href="/Projekti/cart.php">Shporta</a>
                    <a class="<?php echo $activePage === 'orders' ? 'active' : ''; ?>" href="/Projekti/orders.php">Porosite</a>
                    <a class="<?php echo $activePage === 'profile' ? 'active' : ''; ?>" href="/Projekti/profile.php">Profili</a>
                    <a class="nav-action nav-action-logout" href="/Projekti/user-login.php?logout=1"><i class="fa fa-sign-out" aria-hidden="true"></i><span>Logout</span></a>
                <?php endif; ?>
            </nav>
            <button class="nav-toggle" id="navToggle" aria-label="Menu">Menu</button>
        <?php else: ?>
            <nav class="main-nav" id="mainNav">
                <a class="<?php echo $activePage === 'home' ? 'active' : ''; ?>" href="/Projekti/index.php">Shtepi</a>
                <a class="<?php echo $activePage === 'catalog' ? 'active' : ''; ?>" href="/Projekti/catalog.php">Katalogu</a>
                <a class="<?php echo $activePage === 'about' ? 'active' : ''; ?>" href="/Projekti/about.php">Rreth nesh</a>
                <a class="<?php echo $activePage === 'contact' ? 'active' : ''; ?>" href="/Projekti/contact.php">Kontakt</a>
                <a class="<?php echo $activePage === 'blog' ? 'active' : ''; ?>" href="/Projekti/blog.php">Blog</a>
                <a class="<?php echo $activePage === 'xml' ? 'active' : ''; ?>" href="/Projekti/xml.php">XML</a>
                <a class="<?php echo $activePage === 'terms' ? 'active' : ''; ?>" href="/Projekti/terms.php">Kushtet</a>
                <a class="<?php echo $activePage === 'privacy' ? 'active' : ''; ?>" href="/Projekti/privacy.php">Privatesia</a>
                <a class="<?php echo $activePage === 'login' ? 'active' : ''; ?> nav-action" href="/Projekti/user-login.php"><i class="fa fa-sign-in" aria-hidden="true"></i><span>Logimi</span></a>
                <a class="<?php echo $activePage === 'register' ? 'active' : ''; ?>" href="/Projekti/register.php">Regjistrimi</a>
                <a class="<?php echo $activePage === 'admin-login' ? 'active' : ''; ?>" href="/Projekti/admin-login.php">Admin</a>
            </nav>
            <button class="nav-toggle" id="navToggle" aria-label="Menu">Menu</button>
        <?php endif; ?>
    </div>
</header>
<main class="site-main">
