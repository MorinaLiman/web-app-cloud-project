<?php
session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: /Projekti/admin-login.php');
    exit;
}
