<?php
require_once __DIR__ . '/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$timeout = 1800; // 30 minutes

if (isset($_SESSION['user']) && isset($_SESSION['last_activity'])) {
    if (time() - $_SESSION['last_activity'] > $timeout) {
        session_unset();
        session_destroy();

        header('Location: ' . BASE_URL . '/modules/auth/login.php?expired=1');
        exit;
    }
}

if (isset($_SESSION['user'])) {
    $_SESSION['last_activity'] = time();
}

function require_login(): void
{
    if (current_user() === null) {
        set_flash('warning', 'Please log in to access the application.');
        redirect('modules/auth/login.php');
    }
}

function require_guest(): void
{
    if (current_user() !== null) {
        redirect('modules/dashboard/index.php');
    }
}