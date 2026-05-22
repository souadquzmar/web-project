<?php
// actions/delete_property.php
require_once __DIR__ . '/../includes/bootstrap.php';
verify_csrf();
require_login('../index.php');

$id  = (int)($_POST['id'] ?? 0);
$u   = current_user();
$db  = get_db();

if (!$id) {
    set_flash('error', 'Invalid property.');
    header('Location: ../my-listings.php');
    exit;
}

// Only the owner or admin can delete
$row = $db->prepare('SELECT user_id FROM properties WHERE id = ? LIMIT 1');
$row->bind_param('i', $id);
$row->execute();
$prop = $row->get_result()->fetch_assoc();
$row->close();

if (!$prop || ($prop['user_id'] != $u['id'] && $u['role'] !== 'admin')) {
    set_flash('error', 'Permission denied.');
    header('Location: ../my-listings.php');
    exit;
}

// Fetch images to delete files
$imgs = $db->prepare('SELECT filename FROM property_images WHERE property_id = ?');
$imgs->bind_param('i', $id);
$imgs->execute();
$res = $imgs->get_result();
while ($r = $res->fetch_assoc()) {
    $path = __DIR__ . '/../uploads/properties/' . $r['filename'];
    if (file_exists($path)) unlink($path);
}
$imgs->close();

$del = $db->prepare('DELETE FROM properties WHERE id = ?');
$del->bind_param('i', $id);
$del->execute();
$del->close();

set_flash('success', 'Property deleted.');
header('Location: ../my-listings.php');
exit;
