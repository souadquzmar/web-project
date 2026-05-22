<?php
// actions/contact.php
require_once __DIR__ . '/../includes/bootstrap.php';
verify_csrf();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../contact.php');
    exit;
}

$name    = trim(($_POST['first_name'] ?? '') . ' ' . ($_POST['last_name'] ?? ''));
if (empty(trim($name))) $name = trim($_POST['name'] ?? '');
$email   = strtolower(trim($_POST['email']   ?? ''));
$phone   = trim($_POST['phone']   ?? '');
$subject = trim($_POST['subject'] ?? '');
$body    = trim($_POST['body']    ?? '');

if (!$name || !$email || !$body) {
    set_flash('error', 'Please fill in name, email and message.');
    header('Location: ../contact.php');
    exit;
}

$db   = get_db();
$stmt = $db->prepare(
    'INSERT INTO contact_submissions (name, email, phone, subject, body) VALUES (?,?,?,?,?)'
);
$stmt->bind_param('sssss', $name, $email, $phone, $subject, $body);
$stmt->execute();
$stmt->close();

// Send emails
require_once __DIR__ . '/../includes/mail.php';
send_contact_confirmation($email, $name);
send_contact_admin_notification($name, $email, $subject, $body);

set_flash('success', 'Thank you! We will get back to you shortly.');
header('Location: ../contact.php');
exit;
