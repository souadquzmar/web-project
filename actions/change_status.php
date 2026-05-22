<?php
// actions/change_status.php
require_once __DIR__ . '/../includes/bootstrap.php';
verify_csrf();
require_login('../index.php');

$prop_id    = (int)($_POST['property_id'] ?? 0);
$new_status = $_POST['listing_status'] ?? '';
$u          = current_user();
$db         = get_db();

$allowed = ['active', 'pending', 'inactive'];
if (!$prop_id || !in_array($new_status, $allowed)) {
    set_flash('error', 'Invalid request.');
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '../my-listings.php'));
    exit;
}

// Verify ownership (or admin)
$chk = $db->prepare('SELECT user_id FROM properties WHERE id=? LIMIT 1');
$chk->bind_param('i', $prop_id);
$chk->execute();
$row = $chk->get_result()->fetch_assoc();
$chk->close();

if (!$row || ($row['user_id'] != $u['id'] && $u['role'] !== 'admin')) {
    set_flash('error', 'Permission denied.');
    header('Location: ../my-listings.php');
    exit;
}

$stmt = $db->prepare('UPDATE properties SET listing_status=? WHERE id=?');
$stmt->bind_param('si', $new_status, $prop_id);
$stmt->execute();
$stmt->close();

// Email the property owner if status was changed by admin (not self)
if ($row['user_id'] != $u['id']) {
    require_once __DIR__ . '/../includes/mail.php';
    $owner_q = $db->prepare('SELECT u.first_name, u.last_name, u.email, p.title FROM properties p JOIN users u ON u.id=p.user_id WHERE p.id=? LIMIT 1');
    $owner_q->bind_param('i', $prop_id);
    $owner_q->execute();
    $owner = $owner_q->get_result()->fetch_assoc();
    $owner_q->close();
    if ($owner) {
        send_listing_status_email($owner['email'], $owner['first_name'], $owner['title'], $new_status);
    }
}

set_flash('success', 'Listing status updated to ' . ucfirst($new_status) . '.');
header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '../my-listings.php'));
exit;
