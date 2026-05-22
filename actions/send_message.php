<?php
// actions/send_message.php
require_once __DIR__ . '/../includes/bootstrap.php';
verify_csrf();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../listing.php');
    exit;
}

$prop_id = (int)($_POST['property_id'] ?? 0);
$name    = trim($_POST['sender_name']  ?? '');
$email   = strtolower(trim($_POST['sender_email'] ?? ''));
$phone   = trim($_POST['sender_phone'] ?? '');
$body    = trim($_POST['body']         ?? '');

if (!$name || !$email || !$body) {
    set_flash('error', 'Please fill in all required fields.');
    header('Location: ../property.php?id=' . $prop_id);
    exit;
}

$db = get_db();

// Find property owner
$owner_id = null;
if ($prop_id) {
    $o = $db->prepare('SELECT user_id FROM properties WHERE id = ? LIMIT 1');
    $o->bind_param('i', $prop_id);
    $o->execute();
    $row = $o->get_result()->fetch_assoc();
    $o->close();
    $owner_id = $row['user_id'] ?? null;
}

$stmt = $db->prepare(
    'INSERT INTO messages (property_id, sender_name, sender_email, sender_phone, body, owner_id)
     VALUES (?,?,?,?,?,?)'
);
$stmt->bind_param('issssi', $prop_id, $name, $email, $phone, $body, $owner_id);
$stmt->execute();
$stmt->close();

// Email the agent
require_once __DIR__ . '/../includes/mail.php';
if ($owner_id) {
    $agent = $db->prepare('SELECT first_name, last_name, email FROM users WHERE id=? LIMIT 1');
    $agent->bind_param('i', $owner_id);
    $agent->execute();
    $ag = $agent->get_result()->fetch_assoc();
    $agent->close();
    if ($ag) {
        $prop = $db->prepare('SELECT title FROM properties WHERE id=? LIMIT 1');
        $prop->bind_param('i', $prop_id);
        $prop->execute();
        $pr = $prop->get_result()->fetch_assoc();
        $prop->close();
        send_inquiry_notification(
            $ag['email'],
            $ag['first_name'] . ' ' . $ag['last_name'],
            $name, $email,
            $pr['title'] ?? 'Property #' . $prop_id,
            $body
        );
    }
}

set_flash('success', 'Your message has been sent to the agent!');
header('Location: ../property.php?id=' . $prop_id);
exit;
