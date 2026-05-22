<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_login('index.php');

$u = current_user();
$page_title = 'Change Password';
$dash_mode = true;
$dash_active = 'password';
$extra_css = '<link rel="stylesheet" href="css/dashboard.css">';

include __DIR__ . '/includes/header.php';
?>

  <section class="section section-grey">
    <div class="container">
      <div class="dashboard-wrap">
        <?php include __DIR__ . '/includes/dash-sidebar.php'; ?>
        <div class="dash-main">
          <?= render_flash() ?>
          <div class="dash-card dash-card--narrow">
            <div class="dash-card-title">Change Password</div>
            <form action="actions/change_password.php" method="POST">
              <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
              <div class="form-group">
                <label class="form-label">Current Password</label>
                <input class="form-input" type="password" name="current_password" placeholder="Enter current password" required />
              </div>
              <div class="form-group">
                <label class="form-label">New Password</label>
                <input class="form-input" type="password" name="new_password" id="new-password" placeholder="Min. 8 characters" required minlength="8" />
                <div class="password-strength">
                  <div class="strength-bar">
                    <div class="strength-fill" id="strength-fill"></div>
                  </div>
                  <div class="strength-label" id="strength-label"></div>
                </div>
              </div>
              <div class="form-group">
                <label class="form-label">Confirm New Password</label>
                <input class="form-input" type="password" name="confirm_password" id="confirm-password" placeholder="Repeat new password" required />
              </div>
              <button type="submit" class="btn btn-primary" id="updatePasswordBtn"><i class="fa-solid fa-lock"></i> Update Password</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </section>

<?php
$dash_footer = true;
$extra_js = '<script src="js/dashboard.js"></script>';
include __DIR__ . '/includes/footer.php';
?>
