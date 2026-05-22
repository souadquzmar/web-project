<?php
// actions/blog_comment.php
require_once __DIR__ . '/../includes/bootstrap.php';
verify_csrf();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../blog.php');
    exit;
}

$blog_id = (int)($_POST['blog_id'] ?? 0);
$name    = trim($_POST['name']  ?? '');
$email   = strtolower(trim($_POST['email'] ?? ''));
$body    = trim($_POST['body']  ?? '');
$u       = current_user();
$user_id = $u ? $u['id'] : null;

if (!$blog_id || !$name || !$email || !$body) {
    set_flash('error', 'Please fill in all fields.');
    header('Location: ../blog-details.php?id=' . $blog_id);
    exit;
}

$db   = get_db();
$stmt = $db->prepare(
    'INSERT INTO blog_comments (blog_id, user_id, name, email, body) VALUES (?,?,?,?,?)'
);
$stmt->bind_param('iisss', $blog_id, $user_id, $name, $email, $body);
$stmt->execute();
$stmt->close();

set_flash('success', 'Comment posted!');
header('Location: ../blog-details.php?id=' . $blog_id . '#comments');
exit;
