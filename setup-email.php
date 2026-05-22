<?php
// ============================================================
//  setup-email.php — Run this ONCE to download PHPMailer
//  and test your email configuration.
//
//  Access: http://localhost/findhouses/setup-email.php
//  Delete this file after setup is complete.
// ============================================================

echo "<h1>FindHouses Email Setup</h1>";
echo "<style>body{font-family:Montserrat,sans-serif;max-width:700px;margin:40px auto;padding:20px;color:#333}
.ok{color:#22c55e;font-weight:700}.err{color:#ef4444;font-weight:700}
pre{background:#f1f5f9;padding:16px;border-radius:8px;overflow-x:auto}
.step{background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:24px;margin:20px 0}
.step h2{font-size:16px;margin-top:0}</style>";

// ── Step 1: Check if PHPMailer exists ────────────────────────
echo '<div class="step"><h2>Step 1: PHPMailer Status</h2>';

$vendor_dir = __DIR__ . '/vendor/PHPMailer/src/';
if (file_exists($vendor_dir . 'PHPMailer.php')) {
    echo '<p class="ok">✅ PHPMailer is installed!</p>';
} else {
    echo '<p class="err">❌ PHPMailer is NOT installed.</p>';
    echo '<p>Download it manually:</p>';
    echo '<ol>';
    echo '<li>Go to <a href="https://github.com/PHPMailer/PHPMailer/archive/refs/heads/master.zip" target="_blank">https://github.com/PHPMailer/PHPMailer</a> → Download ZIP</li>';
    echo '<li>Extract the ZIP file</li>';
    echo '<li>Copy the <code>src/</code> folder into <code>findhouses/vendor/PHPMailer/src/</code></li>';
    echo '<li>You should have these files:<pre>findhouses/vendor/PHPMailer/src/PHPMailer.php
findhouses/vendor/PHPMailer/src/SMTP.php
findhouses/vendor/PHPMailer/src/Exception.php</pre></li>';
    echo '<li>Refresh this page</li>';
    echo '</ol>';
}
echo '</div>';

// ── Step 2: Check email config ───────────────────────────────
echo '<div class="step"><h2>Step 2: Email Configuration</h2>';

if (file_exists(__DIR__ . '/config/email.php')) {
    require_once __DIR__ . '/config/email.php';
    
    if (SMTP_USER === 'your.email@gmail.com') {
        echo '<p class="err">❌ Email not configured yet.</p>';
        echo '<p>Edit <code>config/email.php</code> and replace:</p>';
        echo '<pre>';
        echo "SMTP_USER → your Gmail address\n";
        echo "SMTP_PASS → your Gmail App Password (NOT your Gmail password)\n";
        echo "SMTP_FROM_EMAIL → same as SMTP_USER\n";
        echo '</pre>';
        echo '<h3>How to get a Gmail App Password:</h3>';
        echo '<ol>';
        echo '<li>Go to <a href="https://myaccount.google.com/security" target="_blank">Google Account Security</a></li>';
        echo '<li>Enable <strong>2-Step Verification</strong> (required)</li>';
        echo '<li>Go to <a href="https://myaccount.google.com/apppasswords" target="_blank">App Passwords</a></li>';
        echo '<li>Create one for "Mail" → "Other" → name it "FindHouses"</li>';
        echo '<li>Copy the 16-character password (like: <code>abcd efgh ijkl mnop</code>)</li>';
        echo '<li>Paste it into <code>config/email.php</code> as SMTP_PASS</li>';
        echo '</ol>';
    } else {
        echo '<p class="ok">✅ Email configured: ' . htmlspecialchars(SMTP_USER) . '</p>';
    }
} else {
    echo '<p class="err">❌ config/email.php not found!</p>';
}
echo '</div>';

// ── Step 3: Send test email ──────────────────────────────────
echo '<div class="step"><h2>Step 3: Test Email</h2>';

if (isset($_POST['test_email'])) {
    $test_to = trim($_POST['test_to']);
    if ($test_to && filter_var($test_to, FILTER_VALIDATE_EMAIL)) {
        require_once __DIR__ . '/includes/bootstrap.php';
        require_once __DIR__ . '/includes/mail.php';
        
        $body = email_wrap('
            <h2 style="font-size:18px;margin-top:0">🎉 Email Test Successful!</h2>
            <p style="line-height:1.7;color:#555">If you\'re reading this, your FindHouses email system is working correctly!</p>
            <p style="line-height:1.7;color:#555">The following email features are now active:</p>
            <ul style="color:#555;line-height:2;font-size:14px">
              <li>✅ Welcome emails on registration</li>
              <li>✅ Password reset emails</li>
              <li>✅ Property inquiry notifications to agents</li>
              <li>✅ Review notifications to property owners</li>
              <li>✅ Contact form confirmations</li>
              <li>✅ Admin notifications for contact submissions</li>
              <li>✅ Listing status change notifications</li>
            </ul>
        ');
        
        $result = send_email($test_to, 'FindHouses Test Email', $body);
        
        if ($result) {
            echo '<p class="ok">✅ Test email sent to ' . htmlspecialchars($test_to) . '! Check your inbox (and spam folder).</p>';
        } else {
            echo '<p class="err">❌ Failed to send. Check your SMTP credentials in config/email.php</p>';
            echo '<p>Common issues:</p>';
            echo '<ul>';
            echo '<li>Wrong App Password (it\'s NOT your Gmail login password)</li>';
            echo '<li>2-Step Verification not enabled on Google account</li>';
            echo '<li>PHPMailer not installed (see Step 1)</li>';
            echo '<li>"Less secure app access" may need to be enabled (older accounts)</li>';
            echo '</ul>';
        }
    }
}

if (file_exists($vendor_dir . 'PHPMailer.php') && defined('SMTP_USER') && SMTP_USER !== 'your.email@gmail.com') {
    echo '<form method="POST">
        <p>Send a test email to verify everything works:</p>
        <input type="text" name="test_to" placeholder="your.email@gmail.com" style="padding:10px;border:1px solid #ddd;border-radius:8px;width:300px;font-size:14px" />
        <button type="submit" name="test_email" value="1" style="padding:10px 20px;background:#ff385c;color:#fff;border:none;border-radius:8px;cursor:pointer;font-weight:700">Send Test</button>
    </form>';
} else {
    echo '<p style="color:#999">Complete Steps 1 and 2 first, then refresh this page to test.</p>';
}
echo '</div>';

// ── Summary ──────────────────────────────────────────────────
echo '<div class="step"><h2>Email Features Connected</h2>';
echo '<table style="width:100%;font-size:14px;border-collapse:collapse">';
$features = [
    ['Registration',       'Welcome email sent to new user',             'actions/register.php'],
    ['Forgot Password',    'Reset link emailed to user',                 'actions/forgot_password.php'],
    ['Contact Form',       'Confirmation to sender + notification to admin', 'actions/contact.php'],
    ['Property Inquiry',   'Agent notified by email when inquiry received', 'actions/send_message.php'],
    ['New Review',         'Property owner notified of new review',       'actions/submit_review.php'],
    ['Status Change',      'Owner notified when admin changes listing status', 'actions/change_status.php'],
];
foreach ($features as $f) {
    echo "<tr style='border-bottom:1px solid #eee'><td style='padding:10px;font-weight:600'>{$f[0]}</td><td style='padding:10px;color:#555'>{$f[1]}</td><td style='padding:10px'><code>{$f[2]}</code></td></tr>";
}
echo '</table></div>';

echo '<p style="text-align:center;color:#999;margin-top:30px">⚠️ Delete this file (setup-email.php) after setup is complete for security.</p>';
