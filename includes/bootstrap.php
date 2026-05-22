<?php
// ============================================================
//  includes/bootstrap.php
//  Include this at the very top of every page.
// ============================================================

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/helpers.php';

// Generate a CSRF token once per session
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

/**
 * Validate CSRF token from POST data.
 * Call this at the start of every action that processes POST.
 */
function verify_csrf(): void
{
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(403);
        die('CSRF validation failed.');
    }
}
