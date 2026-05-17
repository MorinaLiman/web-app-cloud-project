<?php

function setAdminFlash(string $message): void
{
    $_SESSION['admin_flash_success'] = $message;
}

function getAdminFlash(): string
{
    $message = $_SESSION['admin_flash_success'] ?? '';
    unset($_SESSION['admin_flash_success']);

    return (string)$message;
}