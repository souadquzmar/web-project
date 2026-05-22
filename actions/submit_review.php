<?php
// actions/submit_review.php
require_once __DIR__ . '/../includes/bootstrap.php';
verify_csrf();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../listing.php');
    exit;
}

$prop_id = (int)($_POST['property_id'] ?? 0);
$name    = trim($_POST['name']    ?? '');
$email   = strtolower(trim($_POST['email']   ?? ''));
$rating  = max(1, min(5, (int)($_POST['rating'] ?? 5)));
$body    = trim($_POST['body']    ?? '');
$u       = current_user();
$user_id = $u ? $u['id'] : null;

if (!$prop_id || !$name || !$email || !$body) {
    set_flash('error', 'Please fill in all review fields.');
    header('Location: ../property.php?id=' . $prop_id);
    exit;
}

$db   = get_db();
$stmt = $db->prepare(
    'INSERT INTO reviews (property_id, user_id, name, email, rating, body) VALUES (?,?,?,?,?,?)'
);
$stmt->bind_param('iissis', $prop_id, $user_id, $name, $email, $rating, $body);
$stmt->execute();
$stmt->close();

// Notify property owner
require_once __DIR__ . '/../includes/mail.php';
$owner_q = $db->prepare('SELECT u.first_name, u.last_name, u.email, p.title FROM properties p JOIN users u ON u.id=p.user_id WHERE p.id=? LIMIT 1');
$owner_q->bind_param('i', $prop_id);
$owner_q->execute();
$owner = $owner_q->get_result()->fetch_assoc();
$owner_q->close();
if ($owner) {
    send_review_notification($owner['email'], $owner['first_name'], $name, $rating, $owner['title']);
}

set_flash('success', 'Thank you for your review!');
header('Location: ../property.php?id=' . $prop_id . '#reviews');
exit;
