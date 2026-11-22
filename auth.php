<?php
// Jalankan sebelum output apa pun.
session_set_cookie_params(['httponly' => true, 'secure' => !empty($_SERVER['HTTPS']), 'samesite' => 'Lax']);
session_start();
require_once __DIR__ . '/koneksi.php';

// Pastikan instalasi lama tetap memiliki akun admin yang valid.
$pdo->exec("CREATE TABLE IF NOT EXISTS users (id INT AUTO_INCREMENT PRIMARY KEY, username VARCHAR(50) NOT NULL UNIQUE, password VARCHAR(255) NOT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB");
$admin = $pdo->prepare('SELECT id FROM users WHERE username = ?');
$admin->execute(['admin']);
$adminRow = $admin->fetch();
if (!$adminRow) {
    $admin = $pdo->prepare('INSERT INTO users (username, password) VALUES (?, ?)');
    $admin->execute(['admin', password_hash('GantiPassword123!', PASSWORD_DEFAULT)]);
} else {
    $check = $pdo->prepare('SELECT password FROM users WHERE id = ?');
    $check->execute([$adminRow['id']]);
    if (!password_verify('GantiPassword123!', $check->fetchColumn())) {
        $update = $pdo->prepare('UPDATE users SET password = ? WHERE id = ?');
        $update->execute([password_hash('GantiPassword123!', PASSWORD_DEFAULT), $adminRow['id']]);
    }
}

function e($value) { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); }
function csrf() { if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32)); return $_SESSION['csrf']; }
function check_csrf() { if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) { http_response_code(419); exit('Permintaan tidak valid.'); } }
function wajib_login() { if (empty($_SESSION['user_id'])) { header('Location: login.php'); exit; } }
function redirect_msg($message) { header('Location: index.php?pesan=' . urlencode($message)); exit; }
?>