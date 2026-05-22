<?php
// ============================================================
//  includes/mail.php — Email system using PHPMailer + SMTP
//
//  SETUP INSTRUCTIONS:
//  1. Download PHPMailer: https://github.com/PHPMailer/PHPMailer
//  2. Extract to findhouses/vendor/PHPMailer/
//  3. Create a Gmail App Password (see below)
//  4. Update config/email.php with your credentials
// ============================================================

require_once __DIR__ . '/../config/email.php';

// Load PHPMailer (no Composer needed)
$phpmailer_path = __DIR__ . '/../vendor/PHPMailer/src/';
if (file_exists($phpmailer_path . 'PHPMailer.php')) {
    require_once $phpmailer_path . 'Exception.php';
    require_once $phpmailer_path . 'PHPMailer.php';
    require_once $phpmailer_path . 'SMTP.php';
    define('PHPMAILER_AVAILABLE', true);
} else {
    define('PHPMAILER_AVAILABLE', false);
}

/**
 * Create a configured PHPMailer instance.
 * Returns null if PHPMailer is not installed.
 */
function create_mailer(): ?object
{
    if (!PHPMAILER_AVAILABLE) return null;

    $mail = new PHPMailer\PHPMailer\PHPMailer(true);

    $mail->isSMTP();
    $mail->Host       = SMTP_HOST;
    $mail->SMTPAuth   = true;
    $mail->Username   = SMTP_USER;
    $mail->Password   = SMTP_PASS;
    $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = SMTP_PORT;
    $mail->CharSet    = 'UTF-8';

    $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
    $mail->isHTML(true);

    return $mail;
}

/**
 * Send an email. Falls back to PHP mail() if PHPMailer is not installed.
 */
function send_email(string $to, string $subject, string $html_body): bool
{
    $mail = create_mailer();

    if ($mail) {
        try {
            $mail->addAddress($to);
            $mail->Subject = $subject;
            $mail->Body    = $html_body;
            $mail->AltBody = strip_tags(str_replace(['<br>', '<br/>', '<br />'], "\n", $html_body));
            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Email send failed: " . $e->getMessage());
            return false;
        }
    }

    // Fallback: PHP mail()
    $headers  = "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    $headers .= "From: " . SMTP_FROM_NAME . " <" . SMTP_FROM_EMAIL . ">\r\n";
    return @mail($to, $subject, $html_body, $headers);
}

// ─── Styled email wrapper ────────────────────────────────────

function email_wrap(string $content): string
{
    return '
    <div style="max-width:560px;margin:0 auto;font-family:Montserrat,Arial,sans-serif;color:#333">
      <div style="background:linear-gradient(135deg,#ff385c,#ff7090);padding:24px 32px;border-radius:12px 12px 0 0;text-align:center">
        <h1 style="color:#fff;margin:0;font-size:22px">Find<span style="color:#ffe066">.</span>Houses</h1>
      </div>
      <div style="background:#fff;padding:32px;border:1px solid #eee;border-top:none;border-radius:0 0 12px 12px">
        ' . $content . '
        <p style="font-size:12px;color:#bbb;margin-top:28px;border-top:1px solid #eee;padding-top:16px;text-align:center">
          © ' . date('Y') . ' FindHouses · 95 South Park Avenue, New York
        </p>
      </div>
    </div>';
}

function email_button(string $url, string $label): string
{
    return '<div style="text-align:center;margin:28px 0">
      <a href="' . htmlspecialchars($url) . '" style="display:inline-block;background:#ff385c;color:#fff;padding:12px 32px;border-radius:8px;text-decoration:none;font-weight:700;font-size:14px">' . htmlspecialchars($label) . '</a>
    </div>';
}

function site_url(string $path = ''): string
{
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
    // If we're inside /actions/ go up one level
    if (basename($base) === 'actions') $base = dirname($base);
    return "$protocol://$host$base/$path";
}

// ─── Specific email templates ────────────────────────────────

/** Password reset email */
function send_password_reset_email(string $to, string $token): bool
{
    $url = site_url('reset-password.php?token=' . urlencode($token));
    $body = email_wrap('
        <h2 style="font-size:18px;margin-top:0">Password Reset Request</h2>
        <p style="line-height:1.7;color:#555">We received a request to reset your password. Click the button below to choose a new one. This link expires in <strong>1 hour</strong>.</p>
        ' . email_button($url, 'Reset My Password') . '
        <p style="font-size:13px;color:#999;line-height:1.6">If you didn\'t request this, you can safely ignore this email.</p>
    ');
    return send_email($to, 'Reset Your FindHouses Password', $body);
}

/** Welcome email after registration */
function send_welcome_email(string $to, string $first_name): bool
{
    $url = site_url('listing.php');
    $body = email_wrap('
        <h2 style="font-size:18px;margin-top:0">Welcome to FindHouses, ' . htmlspecialchars($first_name) . '!</h2>
        <p style="line-height:1.7;color:#555">Thank you for joining FindHouses! Your account has been created and you\'re ready to start exploring thousands of properties.</p>
        ' . email_button($url, 'Browse Properties') . '
        <p style="font-size:13px;color:#999">Here\'s what you can do:</p>
        <ul style="color:#555;line-height:2;font-size:14px">
          <li>Save your favorite properties</li>
          <li>Contact agents directly</li>
          <li>Leave reviews on listings</li>
          <li>Track your property inquiries</li>
        </ul>
    ');
    return send_email($to, 'Welcome to FindHouses!', $body);
}

/** Notify agent when someone sends a property inquiry */
function send_inquiry_notification(string $agent_email, string $agent_name, string $sender_name, string $sender_email, string $prop_title, string $message): bool
{
    $url = site_url('my-messages.php');
    $body = email_wrap('
        <h2 style="font-size:18px;margin-top:0">New Property Inquiry</h2>
        <p style="line-height:1.7;color:#555">Hi ' . htmlspecialchars($agent_name) . ', you have a new inquiry about your listing:</p>
        <div style="background:#f8f9fa;border-radius:8px;padding:16px;margin:16px 0;border-left:4px solid #ff385c">
          <strong style="font-size:15px">' . htmlspecialchars($prop_title) . '</strong>
        </div>
        <table style="width:100%;font-size:14px;color:#555;line-height:1.8">
          <tr><td style="padding:4px 0"><strong>From:</strong></td><td>' . htmlspecialchars($sender_name) . '</td></tr>
          <tr><td style="padding:4px 0"><strong>Email:</strong></td><td><a href="mailto:' . htmlspecialchars($sender_email) . '" style="color:#ff385c">' . htmlspecialchars($sender_email) . '</a></td></tr>
          <tr><td style="padding:4px 0;vertical-align:top"><strong>Message:</strong></td><td>' . nl2br(htmlspecialchars($message)) . '</td></tr>
        </table>
        ' . email_button($url, 'View All Messages') . '
    ');
    return send_email($agent_email, 'New Inquiry: ' . $prop_title, $body);
}

/** Notify property owner when a new review is posted */
function send_review_notification(string $owner_email, string $owner_name, string $reviewer_name, int $rating, string $prop_title): bool
{
    $stars = str_repeat('★', $rating) . str_repeat('☆', 5 - $rating);
    $url = site_url('dashboard.php');
    $body = email_wrap('
        <h2 style="font-size:18px;margin-top:0">New Review on Your Property</h2>
        <p style="line-height:1.7;color:#555">Hi ' . htmlspecialchars($owner_name) . ', your property received a new review!</p>
        <div style="background:#f8f9fa;border-radius:8px;padding:16px;margin:16px 0">
          <strong>' . htmlspecialchars($prop_title) . '</strong><br>
          <span style="color:#f59e0b;font-size:18px">' . $stars . '</span> by ' . htmlspecialchars($reviewer_name) . '
        </div>
        ' . email_button($url, 'View Dashboard') . '
    ');
    return send_email($owner_email, 'New Review: ' . $prop_title, $body);
}

/** Notify user when their contact form is received */
function send_contact_confirmation(string $to, string $name): bool
{
    $body = email_wrap('
        <h2 style="font-size:18px;margin-top:0">We Got Your Message!</h2>
        <p style="line-height:1.7;color:#555">Hi ' . htmlspecialchars($name) . ', thank you for reaching out to FindHouses. We\'ve received your message and our team will get back to you within <strong>1 business day</strong>.</p>
        <p style="line-height:1.7;color:#555">In the meantime, feel free to browse our latest listings.</p>
        ' . email_button(site_url('listing.php'), 'Browse Properties') . '
    ');
    return send_email($to, 'We received your message — FindHouses', $body);
}

/** Notify admin of new contact form submission */
function send_contact_admin_notification(string $name, string $email, string $subject, string $message): bool
{
    $body = email_wrap('
        <h2 style="font-size:18px;margin-top:0">New Contact Form Submission</h2>
        <table style="width:100%;font-size:14px;color:#555;line-height:1.8">
          <tr><td style="padding:4px 0;width:100px"><strong>Name:</strong></td><td>' . htmlspecialchars($name) . '</td></tr>
          <tr><td style="padding:4px 0"><strong>Email:</strong></td><td><a href="mailto:' . htmlspecialchars($email) . '" style="color:#ff385c">' . htmlspecialchars($email) . '</a></td></tr>
          <tr><td style="padding:4px 0"><strong>Subject:</strong></td><td>' . htmlspecialchars($subject) . '</td></tr>
          <tr><td style="padding:4px 0;vertical-align:top"><strong>Message:</strong></td><td>' . nl2br(htmlspecialchars($message)) . '</td></tr>
        </table>
    ');
    // Send to all admins
    $db = get_db();
    $admins = $db->query("SELECT email FROM users WHERE role='admin'")->fetch_all(MYSQLI_ASSOC);
    $sent = false;
    foreach ($admins as $admin) {
        $sent = send_email($admin['email'], 'Contact Form: ' . $subject, $body) || $sent;
    }
    return $sent;
}

/** Notify user when their property listing is approved/status changed */
function send_listing_status_email(string $to, string $owner_name, string $prop_title, string $new_status): bool
{
    $status_colors = ['active' => '#22c55e', 'pending' => '#f59e0b', 'inactive' => '#ef4444'];
    $color = $status_colors[$new_status] ?? '#6b7280';
    $body = email_wrap('
        <h2 style="font-size:18px;margin-top:0">Listing Status Updated</h2>
        <p style="line-height:1.7;color:#555">Hi ' . htmlspecialchars($owner_name) . ', the status of your property listing has been updated:</p>
        <div style="background:#f8f9fa;border-radius:8px;padding:16px;margin:16px 0;text-align:center">
          <strong style="font-size:15px">' . htmlspecialchars($prop_title) . '</strong><br>
          <span style="display:inline-block;margin-top:8px;padding:4px 16px;border-radius:20px;font-size:13px;font-weight:700;color:#fff;background:' . $color . '">' . ucfirst($new_status) . '</span>
        </div>
        ' . email_button(site_url('my-listings.php'), 'View My Listings') . '
    ');
    return send_email($to, 'Listing Update: ' . $prop_title, $body);
}
