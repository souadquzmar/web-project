<?php
// ============================================================
//  includes/helpers.php  —  Shared utility functions
// ============================================================

/** HTML-escape a string */
function e(string $s): string
{
    return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Format price as $1,234,000 or $1,234 / month */
function format_price(float $price, string $status): string
{
    $formatted = '$' . number_format($price, 0);
    return $status === 'For Rent' ? $formatted . ' / month' : $formatted . ' / listing';
}

/** Human-readable time ago */
function time_ago(string $datetime): string
{
    $diff = time() - strtotime($datetime);
    if ($diff < 60)         return 'just now';
    if ($diff < 3600)       return floor($diff / 60) . ' min ago';
    if ($diff < 86400)      return floor($diff / 3600) . ' hr ago';
    if ($diff < 604800)     return floor($diff / 86400) . ' days ago';
    return date('M j, Y', strtotime($datetime));
}

/** Flash message helpers */
function set_flash(string $type, string $msg): void
{
    $_SESSION['flash'] = ['type' => $type, 'msg' => $msg];
}

function get_flash(): ?array
{
    if (isset($_SESSION['flash'])) {
        $f = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $f;
    }
    return null;
}

/** Render a Bootstrap-style alert from flash */
function render_flash(): string
{
    $f = get_flash();
    if (!$f) return '';
    $cls = $f['type'] === 'success' ? 'success' : ($f['type'] === 'error' ? 'danger' : 'info');
    return '<div class="alert alert-' . $cls . ' alert-dismissible fade show" role="alert">'
         . e($f['msg'])
         . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
}

/** Sanitise a filename for uploads */
function safe_filename(string $original): string
{
    $ext  = strtolower(pathinfo($original, PATHINFO_EXTENSION));
    $name = bin2hex(random_bytes(12));
    return $name . '.' . $ext;
}

/** Property image URL helper */
function prop_img_url(string $filename): string
{
    $uploaded = __DIR__ . '/../uploads/properties/' . $filename;
    if (file_exists($uploaded)) {
        return 'uploads/properties/' . $filename;
    }
    // Fall back to the original project images
    return 'img/featured-properties/' . $filename;
}

/** Avatar URL helper — returns uploaded avatar or a consistent default */
function avatar_url(?string $filename, ?int $user_id = null): string
{
    if ($filename) {
        $uploaded = __DIR__ . '/../uploads/avatars/' . $filename;
        if (file_exists($uploaded)) {
            return 'uploads/avatars/' . $filename;
        }
    }
    // Consistent default: pick from available defaults based on user_id
    $defaults = ['img/agents/t-1.jpg','img/agents/t-2.jpg','img/agents/t-3.jpg','img/agents/t-4.jpg','img/agents/t-5.jpg','img/agents/t-6.jpg'];
    $idx = $user_id ? ($user_id % count($defaults)) : 0;
    return $defaults[$idx];
}

/** Star rating HTML */
function stars(int $rating): string
{
    $s = '';
    for ($i = 1; $i <= 5; $i++) {
        $s .= $i <= $rating ? '★' : '☆';
    }
    return $s;
}

/** Pagination helper — returns array of page links info */
function paginate(int $total, int $per_page, int $current_page): array
{
    $total_pages = (int)ceil($total / $per_page);
    return [
        'total'        => $total,
        'per_page'     => $per_page,
        'current_page' => $current_page,
        'total_pages'  => $total_pages,
        'offset'       => ($current_page - 1) * $per_page,
    ];
}

/**
 * Get all property IDs favorited by the current user.
 * Returns an array of property IDs for quick lookup.
 */
function get_user_favorites(): array
{
    if (!is_logged_in()) return [];
    static $cache = null;
    if ($cache !== null) return $cache;
    $u = current_user();
    $db = get_db();
    $res = $db->query("SELECT property_id FROM favorites WHERE user_id={$u['id']}");
    $cache = [];
    while ($row = $res->fetch_assoc()) {
        $cache[] = (int)$row['property_id'];
    }
    return $cache;
}

/** Check if a specific property is favorited by current user */
function is_favorited(int $property_id): bool
{
    return in_array($property_id, get_user_favorites());
}
