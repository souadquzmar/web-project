<?php
// actions/change_password.php
require_once __DIR__ . '/../includes/bootstrap.php';
verify_csrf();
require_login('../index.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../change-password.php');
    exit;
}

$u       = current_user();
$uid     = $u['id'];
$db      = get_db();

$current = $_POST['current_password'] ?? '';
$new_pw  = $_POST['new_password']     ?? '';
$confirm = $_POST['confirm_password'] ?? '';

if (!$current || !$new_pw || !$confirm) {
    set_flash('error', 'All fields are required.');
    header('Location: ../change-password.php');
    exit;
}
if ($new_pw !== $confirm) {
    set_flash('error', 'New passwords do not match.');
    header('Location: ../change-password.php');
    exit;
}
if (strlen($new_pw) < 8) {
    set_flash('error', 'Password must be at least 8 characters.');
    header('Location: ../change-password.php');
    exit;
}

// Verify current password
$sel = $db->prepare('SELECT password_hash FROM users WHERE id = ? LIMIT 1');
$sel->bind_param('i', $uid);
$sel->execute();
$row = $sel->get_result()->fetch_assoc();
$sel->close();

if (!password_verify($current, $row['password_hash'])) {
    set_flash('error', 'Current password is incorrect.');
    header('Location: ../change-password.php');
    exit;
}

$hash = password_hash($new_pw, PASSWORD_BCRYPT);
$upd  = $db->prepare('UPDATE users SET password_hash=? WHERE id=?');
$upd->bind_param('si', $hash, $uid);
$upd->execute();
$upd->close();

set_flash('success', 'Password changed successfully!');
header('Location: ../change-password.php');
exit;
