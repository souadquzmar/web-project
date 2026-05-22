<?php
// actions/add_property.php
require_once __DIR__ . '/../includes/bootstrap.php';
verify_csrf();
require_login('../index.php');

// Only agents and admins can add properties
if (!is_agent_or_admin()) {
    set_flash('error', 'Only agents and admins can add properties.');
    header('Location: ../dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit;
}

$u    = current_user();
$uid  = $u['id'];
$db   = get_db();

// Sanitise text fields
$title       = trim($_POST['title']       ?? '');
$description = trim($_POST['description'] ?? '');
$status      = in_array($_POST['status'] ?? '', ['For Sale','For Rent']) ? $_POST['status'] : 'For Sale';
$type        = in_array($_POST['type'] ?? '', ['House','Apartment','Commercial','Lot','Garage','Villa']) ? $_POST['type'] : 'House';
$price       = (float)($_POST['price']    ?? 0);
$area        = !empty($_POST['area_sqft']) ? (float)$_POST['area_sqft'] : null;
$bedrooms    = !empty($_POST['bedrooms'])  ? (int)$_POST['bedrooms']    : null;
$bathrooms   = !empty($_POST['bathrooms']) ? (int)$_POST['bathrooms']   : null;
$rooms       = !empty($_POST['rooms'])     ? (int)$_POST['rooms']       : null;
$prop_age    = trim($_POST['property_age'] ?? '');
$address     = trim($_POST['address']     ?? '');
$city        = trim($_POST['city']        ?? '');
$state       = trim($_POST['state']       ?? '');
$country     = trim($_POST['country']     ?? '');
$latitude    = !empty($_POST['latitude'])  ? (float)$_POST['latitude']  : null;
$longitude   = !empty($_POST['longitude']) ? (float)$_POST['longitude'] : null;

if (!$title || !$description || $price <= 0) {
    set_flash('error', 'Please fill in all required fields.');
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '../index.php'));
    exit;
}

// Handle image uploads
$upload_dir = __DIR__ . '/../uploads/properties/';
$cover_image = null;
$uploaded_files = [];

if (!empty($_FILES['images']['name'][0])) {
    $allowed_ext = ['jpg','jpeg','png','gif','webp'];
    foreach ($_FILES['images']['tmp_name'] as $i => $tmp) {
        if ($_FILES['images']['error'][$i] !== UPLOAD_ERR_OK) continue;
        $orig_name = $_FILES['images']['name'][$i];
        $ext = strtolower(pathinfo($orig_name, PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed_ext)) continue;
        $new_name = bin2hex(random_bytes(12)) . '.' . $ext;
        if (move_uploaded_file($tmp, $upload_dir . $new_name)) {
            $uploaded_files[] = $new_name;
            if ($cover_image === null) $cover_image = $new_name;
        }
    }
}

// Insert property
$stmt = $db->prepare(
    'INSERT INTO properties
       (user_id, title, description, status, type, price, area_sqft,
        bedrooms, bathrooms, rooms, property_age,
        address, city, state, country, latitude, longitude, cover_image)
     VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)'
);
$stmt->bind_param(
    'issssddiiisssssdds',
    $uid, $title, $description, $status, $type,
    $price, $area,
    $bedrooms, $bathrooms, $rooms, $prop_age,
    $address, $city, $state, $country,
    $latitude, $longitude,
    $cover_image
);
$stmt->execute();
$prop_id = $stmt->insert_id;
$stmt->close();

// Insert images
if ($prop_id && !empty($uploaded_files)) {
    foreach ($uploaded_files as $idx => $fname) {
        $img = $db->prepare('INSERT INTO property_images (property_id, filename, sort_order) VALUES (?,?,?)');
        $img->bind_param('isi', $prop_id, $fname, $idx);
        $img->execute();
        $img->close();
    }
}

// Insert features
if ($prop_id && !empty($_POST['features'])) {
    foreach ($_POST['features'] as $feat) {
        $feat = trim($feat);
        if (!$feat) continue;
        $f = $db->prepare('INSERT IGNORE INTO property_features (property_id, feature) VALUES (?,?)');
        $f->bind_param('is', $prop_id, $feat);
        $f->execute();
        $f->close();
    }
}

set_flash('success', 'Your property listing has been added successfully!');
header('Location: ../property.php?id=' . $prop_id);
exit;
