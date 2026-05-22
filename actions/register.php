<?php
// actions/register.php
require_once __DIR__ . '/../includes/bootstrap.php';
verify_csrf();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit;
}

$first = trim($_POST['first_name'] ?? '');
$last  = trim($_POST['last_name']  ?? '');
$email = strtolower(trim($_POST['email']    ?? ''));
$pass  = $_POST['password'] ?? '';

if (!$first || !$last || !$email || !$pass) {
    set_flash('error', 'All fields are required.');
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '../index.php'));
    exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    set_flash('error', 'Please enter a valid email address.');
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '../index.php'));
    exit;
}
if (strlen($pass) < 8) {
    set_flash('error', 'Password must be at least 8 characters.');
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '../index.php'));
    exit;
}

$db = get_db();

// Check duplicate email
$chk = $db->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
$chk->bind_param('s', $email);
$chk->execute();
if ($chk->get_result()->num_rows > 0) {
    $chk->close();
    set_flash('error', 'That email address is already registered.');
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '../index.php'));
    exit;
}
$chk->close();

// Build username from first+last, ensure unique
$base_username = strtolower($first . $last);
$base_username = preg_replace('/[^a-z0-9]/', '', $base_username);
$username = $base_username;
$n = 1;
while (true) {
    $u = $db->prepare('SELECT id FROM users WHERE username = ? LIMIT 1');
    $u->bind_param('s', $username);
    $u->execute();
    if ($u->get_result()->num_rows === 0) { $u->close(); break; }
    $u->close();
    $username = $base_username . $n++;
}

$hash = password_hash($pass, PASSWORD_BCRYPT);
$ins  = $db->prepare(
    'INSERT INTO users (first_name, last_name, username, email, password_hash) VALUES (?,?,?,?,?)'
);
$ins->bind_param('sssss', $first, $last, $username, $email, $hash);

if (!$ins->execute()) {
    $ins->close();
    set_flash('error', 'Registration failed. Please try again.');
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '../index.php'));
    exit;
}
$new_id = $ins->insert_id;
$ins->close();

// Fetch the new user and log them in
$sel = $db->prepare('SELECT * FROM users WHERE id = ? LIMIT 1');
$sel->bind_param('i', $new_id);
$sel->execute();
$user = $sel->get_result()->fetch_assoc();
$sel->close();

login_user($user);

// Send welcome email
require_once __DIR__ . '/../includes/mail.php';
send_welcome_email($email, $first);

set_flash('success', 'Welcome to FindHouses, ' . $first . '!');
header('Location: ../dashboard.php');
exit;
