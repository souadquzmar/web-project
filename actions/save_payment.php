<?php
// actions/save_payment.php
require_once __DIR__ . '/../includes/bootstrap.php';
verify_csrf();
require_login('../index.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../payment-method.php');
    exit;
}

$u          = current_user();
$uid        = $u['id'];
$db         = get_db();

$card_num   = preg_replace('/\D/', '', $_POST['card_number']  ?? '');
$last_four  = substr($card_num, -4);
$cardholder = trim($_POST['cardholder']   ?? '');
$card_type  = trim($_POST['card_type']    ?? 'Visa');
$set_default = isset($_POST['set_default']) ? 1 : 0;

// Parse expiry — accept "MM / YY", "MM/YY", or separate month/year fields
$exp_month  = trim($_POST['expiry_month'] ?? '');
$exp_year   = trim($_POST['expiry_year']  ?? '');
if (!$exp_month && !$exp_year && !empty($_POST['expiry'])) {
    $parts = preg_split('/\s*\/\s*/', $_POST['expiry']);
    $exp_month = $parts[0] ?? '';
    $exp_year  = $parts[1] ?? '';
}

// Auto-detect card type from card number
if (strlen($card_num) >= 1) {
    if ($card_num[0] === '4') $card_type = 'Visa';
    elseif ($card_num[0] === '5') $card_type = 'Mastercard';
    elseif ($card_num[0] === '3') $card_type = 'Amex';
}

if (!$last_four || !$cardholder || !$exp_month || !$exp_year) {
    set_flash('error', 'Please fill in all card details.');
    header('Location: ../payment-method.php');
    exit;
}

if ($set_default) {
    // Unset all current defaults for this user
    $db->query("UPDATE payment_methods SET is_default=0 WHERE user_id=$uid");
}

$stmt = $db->prepare(
    'INSERT INTO payment_methods (user_id, card_type, last_four, cardholder, expiry_month, expiry_year, is_default)
     VALUES (?,?,?,?,?,?,?)'
);
$stmt->bind_param('isssssi', $uid, $card_type, $last_four, $cardholder, $exp_month, $exp_year, $set_default);
$stmt->execute();
$stmt->close();

set_flash('success', 'Payment method saved!');
header('Location: ../payment-method.php');
exit;
