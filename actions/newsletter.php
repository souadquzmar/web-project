<?php
// actions/newsletter.php
require_once __DIR__ . '/../includes/bootstrap.php';
verify_csrf();

$email = strtolower(trim($_POST['email'] ?? ''));
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    set_flash('error', 'Please enter a valid email address.');
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '../index.php'));
    exit;
}

$db   = get_db();
$stmt = $db->prepare('INSERT IGNORE INTO newsletter (email) VALUES (?)');
$stmt->bind_param('s', $email);
$stmt->execute();
$stmt->close();

set_flash('success', 'You have subscribed to our newsletter!');
header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '../index.php'));
exit;
