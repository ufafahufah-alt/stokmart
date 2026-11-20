<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Keluar hanya lewat tombol resmi (POST + token CSRF), supaya tidak bisa
// dipicu diam-diam dari tautan atau gambar di situs lain.
$sah = $_SERVER['REQUEST_METHOD'] === 'POST'
    && !empty($_SESSION['csrf'])
    && hash_equals($_SESSION['csrf'], $_POST['csrf'] ?? '');

if (!$sah) {
    header("Location: " . (!empty($_SESSION['login']) ? "index.php" : "login.php"));
    exit();
}

// 1. Kosongkan semua variabel sesi
$_SESSION = array();

// 2. Hapus cookie sesi di browser
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 3. Hancurkan sesi di server
session_destroy();

// 4. Kembali ke halaman login dengan pesan
header("Location: login.php?keluar=1");
exit();
