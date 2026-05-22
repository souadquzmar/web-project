<?php
require_once __DIR__ . '/includes/bootstrap.php';

$token = trim($_GET['token'] ?? '');
$valid = false;

if ($token) {
    $db = get_db();
    $stmt = $db->prepare('SELECT * FROM password_resets WHERE token=? AND used=0 AND expires_at > NOW() LIMIT 1');
    $stmt->bind_param('s', $token);
    $stmt->execute();
    $valid = $stmt->get_result()->num_rows > 0;
    $stmt->close();
}

$page_title = 'Reset Password';
$nav_active = '';
$extra_css = '';
include __DIR__ . '/includes/header.php';
?>

  <div class="page-hero">
    <div class="container">
      <h1>Reset Password</h1>
      <div class="breadcrumb-nav"><a href="index.php">Home</a><span class="sep">›</span><span class="current">Reset Password</span></div>
    </div>
  </div>

  <section class="section section-grey">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-5">
          <div class="contact-form-card">
            <?= render_flash() ?>
            <?php if ($valid): ?>
            <h3 style="margin-bottom:20px">Choose a New Password</h3>
            <form action="actions/reset_password.php" method="POST">
              <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
              <input type="hidden" name="token" value="<?= e($token) ?>">
              <div class="form-group">
                <label class="form-label">New Password</label>
                <input class="form-input" type="password" name="new_password" placeholder="Min. 8 characters" required minlength="8" />
              </div>
              <div class="form-group">
                <label class="form-label">Confirm Password</label>
                <input class="form-input" type="password" name="confirm_password" placeholder="Repeat password" required />
              </div>
              <button class="btn btn-primary" type="submit" style="width:100%;justify-content:center"><i class="fa-solid fa-lock"></i> Reset Password</button>
            </form>
            <?php else: ?>
            <h3 style="margin-bottom:20px">Invalid or Expired Link</h3>
            <p style="color:var(--textLight);line-height:1.7">This password reset link has expired or has already been used. Please request a new one.</p>
            <a href="index.php" class="btn btn-primary" style="margin-top:16px"><i class="fa-solid fa-arrow-left"></i> Back to Home</a>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </section>

<?php include __DIR__ . '/includes/footer.php'; ?>
