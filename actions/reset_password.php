<?php
// actions/reset_password.php
require_once __DIR__ . '/../includes/bootstrap.php';
verify_csrf();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit;
}

$token    = trim($_POST['token'] ?? '');
$password = $_POST['new_password'] ?? '';
$confirm  = $_POST['confirm_password'] ?? '';

if (!$token) {
    set_flash('error', 'Invalid reset link.');
    header('Location: ../index.php');
    exit;
}

if (strlen($password) < 8) {
    set_flash('error', 'Password must be at least 8 characters.');
    header('Location: ../reset-password.php?token=' . urlencode($token));
    exit;
}

if ($password !== $confirm) {
    set_flash('error', 'Passwords do not match.');
    header('Location: ../reset-password.php?token=' . urlencode($token));
    exit;
}

$db = get_db();

// Verify token is valid and not expired
$stmt = $db->prepare('SELECT * FROM password_resets WHERE token=? AND used=0 AND expires_at > NOW() LIMIT 1');
$stmt->bind_param('s', $token);
$stmt->execute();
$reset = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$reset) {
    set_flash('error', 'This reset link has expired or already been used. Please request a new one.');
    header('Location: ../index.php');
    exit;
}

// Update the user's password
$hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
$stmt = $db->prepare('UPDATE users SET password_hash=? WHERE email=?');
$stmt->bind_param('ss', $hash, $reset['email']);
$stmt->execute();
$stmt->close();

// Mark token as used
$db->query("UPDATE password_resets SET used=1 WHERE id={$reset['id']}");

set_flash('success', 'Your password has been reset! You can now log in with your new password.');
header('Location: ../index.php');
exit;
