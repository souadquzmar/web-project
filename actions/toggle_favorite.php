<?php
// actions/toggle_favorite.php
require_once __DIR__ . '/../includes/bootstrap.php';
verify_csrf();
require_login('../index.php');

$prop_id = (int)($_POST['property_id'] ?? 0);
$u       = current_user();
$db      = get_db();

if (!$prop_id) {
    set_flash('error', 'Invalid property.');
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '../listing.php'));
    exit;
}

// Check if already favorited
$chk = $db->prepare('SELECT 1 FROM favorites WHERE user_id=? AND property_id=?');
$chk->bind_param('ii', $u['id'], $prop_id);
$chk->execute();
$exists = $chk->get_result()->num_rows > 0;
$chk->close();

if ($exists) {
    $del = $db->prepare('DELETE FROM favorites WHERE user_id=? AND property_id=?');
    $del->bind_param('ii', $u['id'], $prop_id);
    $del->execute();
    $del->close();
    set_flash('success', 'Removed from favorites.');
} else {
    $ins = $db->prepare('INSERT IGNORE INTO favorites (user_id, property_id) VALUES (?,?)');
    $ins->bind_param('ii', $u['id'], $prop_id);
    $ins->execute();
    $ins->close();
    set_flash('success', 'Added to favorites!');
}

header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '../listing.php'));
exit;
