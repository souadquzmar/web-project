<?php
// actions/update_profile.php
require_once __DIR__ . '/../includes/bootstrap.php';
verify_csrf();
require_login('../index.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../user-profile.php');
    exit;
}

$u       = current_user();
$uid     = $u['id'];
$db      = get_db();

$first   = trim($_POST['first_name'] ?? '');
$last    = trim($_POST['last_name']  ?? '');
$phone   = trim($_POST['phone']      ?? '');
$address = trim($_POST['address']    ?? '');
$about   = trim($_POST['about']      ?? '');

if (!$first || !$last) {
    set_flash('error', 'First and last name are required.');
    header('Location: ../user-profile.php');
    exit;
}

// Handle avatar upload
$avatar = $u['avatar'];
if (!empty($_FILES['avatar']['name'])) {
    $allowed = ['jpg','jpeg','png','gif','webp'];
    $ext = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
    if (in_array($ext, $allowed) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $new_name   = bin2hex(random_bytes(12)) . '.' . $ext;
        $upload_dir = __DIR__ . '/../uploads/avatars/';
        if (move_uploaded_file($_FILES['avatar']['tmp_name'], $upload_dir . $new_name)) {
            // Delete old avatar if not default
            if ($avatar !== 'default-avatar.jpg') {
                $old = $upload_dir . $avatar;
                if (file_exists($old)) unlink($old);
            }
            $avatar = $new_name;
        }
    }
}

$stmt = $db->prepare(
    'UPDATE users SET first_name=?, last_name=?, phone=?, address=?, about=?, avatar=? WHERE id=?'
);
$stmt->bind_param('ssssssi', $first, $last, $phone, $address, $about, $avatar, $uid);
$stmt->execute();
$stmt->close();

// Refresh session
$_SESSION['user_first_name'] = $first;
$_SESSION['user_last_name']  = $last;
$_SESSION['user_avatar']     = $avatar;

set_flash('success', 'Profile updated successfully!');
header('Location: ../user-profile.php');
exit;
