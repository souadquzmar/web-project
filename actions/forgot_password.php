<?php
// actions/forgot_password.php
require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/mail.php';
verify_csrf();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit;
}

$email = strtolower(trim($_POST['email'] ?? ''));

if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    set_flash('error', 'Please enter a valid email address.');
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '../index.php'));
    exit;
}

$db = get_db();

// Check if user exists
$stmt = $db->prepare('SELECT id, email FROM users WHERE email=? LIMIT 1');
$stmt->bind_param('s', $email);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Always show success message (don't reveal if email exists)
if ($user) {
    // Generate secure token
    $token = bin2hex(random_bytes(32));
    $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

    // Invalidate any previous tokens for this email
    $db->query("UPDATE password_resets SET used=1 WHERE email='" . $db->real_escape_string($email) . "' AND used=0");

    // Store token
    $stmt = $db->prepare('INSERT INTO password_resets (email, token, expires_at) VALUES (?,?,?)');
    $stmt->bind_param('sss', $email, $token, $expires);
    $stmt->execute();
    $stmt->close();

    // Send email
    send_password_reset_email($email, $token);
}

set_flash('success', 'If an account with that email exists, we\'ve sent a password reset link. Please check your inbox.');
header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '../index.php'));
exit;
