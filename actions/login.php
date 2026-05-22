<?php
// actions/login.php
require_once __DIR__ . '/../includes/bootstrap.php';
verify_csrf();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit;
}

$login_id = trim($_POST['login_id'] ?? '');
$password = $_POST['password'] ?? '';
$remember = isset($_POST['remember']);

if (!$login_id || !$password) {
    set_flash('error', 'Please enter your credentials.');
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '../index.php'));
    exit;
}

$db = get_db();
$stmt = $db->prepare(
    'SELECT * FROM users WHERE email = ? OR username = ? LIMIT 1'
);
$stmt->bind_param('ss', $login_id, $login_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user || !password_verify($password, $user['password_hash'])) {
    set_flash('error', 'Invalid email/username or password.');
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '../index.php'));
    exit;
}

login_user($user);

// Persistent cookie (30 days) if "remember me" checked
if ($remember) {
    $token = bin2hex(random_bytes(32));
    setcookie('remember_token', $token, time() + 30 * 86400, '/', '', false, true);
}

set_flash('success', 'Welcome back, ' . $user['first_name'] . '!');
header('Location: ../dashboard.php');
exit;
