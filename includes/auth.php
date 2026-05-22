<?php
// ============================================================
//  includes/auth.php  —  Session helpers & auth guard
// ============================================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/** True if a user is logged in */
function is_logged_in(): bool
{
    return isset($_SESSION['user_id']) && $_SESSION['user_id'] > 0;
}

/** Returns the current user array from session, or null */
function current_user(): ?array
{
    if (!is_logged_in()) return null;
    return [
        'id'         => $_SESSION['user_id'],
        'first_name' => $_SESSION['user_first_name'] ?? '',
        'last_name'  => $_SESSION['user_last_name']  ?? '',
        'email'      => $_SESSION['user_email']      ?? '',
        'role'       => $_SESSION['user_role']       ?? 'user',
        'avatar'     => $_SESSION['user_avatar']     ?? 'default-avatar.jpg',
        'username'   => $_SESSION['user_username']   ?? '',
    ];
}

/** Redirect to login if not authenticated */
function require_login(string $redirect = 'index.php'): void
{
    if (!is_logged_in()) {
        header('Location: ' . $redirect . '?msg=login_required');
        exit;
    }
}

/** Populate session from a users row */
function login_user(array $row): void
{
    session_regenerate_id(true);
    $_SESSION['user_id']         = (int)$row['id'];
    $_SESSION['user_first_name'] = $row['first_name'];
    $_SESSION['user_last_name']  = $row['last_name'];
    $_SESSION['user_email']      = $row['email'];
    $_SESSION['user_role']       = $row['role'];
    $_SESSION['user_avatar']     = $row['avatar'];
    $_SESSION['user_username']   = $row['username'];
}

/** Destroy the session (logout) */
function logout_user(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
}

/** Check if current user has a specific role */
function is_role(string $role): bool
{
    return ($_SESSION['user_role'] ?? '') === $role;
}

/** Check if current user is admin */
function is_admin(): bool
{
    return is_role('admin');
}

/** Check if current user is agent */
function is_agent(): bool
{
    return is_role('agent');
}

/** Check if current user is agent OR admin */
function is_agent_or_admin(): bool
{
    return is_admin() || is_agent();
}

/** Require a specific role — redirect if not authorized */
function require_role(string $role, string $redirect = 'dashboard.php'): void
{
    require_login('index.php');
    if (!is_role($role) && !is_admin()) {
        set_flash('error', 'You do not have permission to access that page.');
        header('Location: ' . $redirect);
        exit;
    }
}
