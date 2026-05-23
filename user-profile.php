<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_login('index.php');

$u = current_user();
$db = get_db();
$stmt = $db->prepare("SELECT * FROM users WHERE id=? LIMIT 1");
$stmt->bind_param('i', $u['id']);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

$page_title = 'Edit Profile';
$dash_mode = true;
$dash_active = 'profile';
$extra_css = '<link rel="stylesheet" href="css/dashboard.css">';

include __DIR__ . '/includes/header.php';
?>

  <section class="section section-grey">
    <div class="container">
      <div class="dashboard-wrap">
        <?php include __DIR__ . '/includes/dash-sidebar.php'; ?>
        <div class="dash-main">
          <?= render_flash() ?>
          <div class="dash-card">
            <div class="dash-card-title">Personal Information</div>
            <form action="actions/update_profile.php" method="POST" enctype="multipart/form-data">
              <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
              <div class="profile-avatar-wrap">
                <div class="avatar-img-wrap">
                  <img src="<?= e(avatar_url($user['avatar'] ?? '', $user['id'] ?? 0)) ?>" alt="Avatar" id="profileAvatarImg" />
                  <div id="avatarOverlay" class="avatar-overlay">
                    <i class="fa-solid fa-camera"></i>
                  </div>
                  <input type="file" name="avatar" id="avatarFileInput" accept="image/*" style="display:none" />
                </div>
                <div>
                  <div class="profile-name"><?= e($user['first_name'] . ' ' . $user['last_name']) ?></div>
                  <div class="profile-since">Real Estate <?= e(ucfirst($user['role'])) ?> since 2020</div>
                  <button type="button" class="avatar-upload-btn" id="changePhotoBtn"><i class="fa-solid fa-camera"></i> Change Photo</button>
                </div>
              </div>
              <div class="form-row">
                <div class="form-group"><label class="form-label">First Name</label><input class="form-input" type="text" name="first_name" value="<?= e($user['first_name']) ?>" required /></div>
                <div class="form-group"><label class="form-label">Last Name</label><input class="form-input" type="text" name="last_name" value="<?= e($user['last_name']) ?>" required /></div>
              </div>
              <div class="form-row">
                <div class="form-group"><label class="form-label">Email Address</label><input class="form-input" type="email" value="<?= e($user['email']) ?>" disabled /></div>
                <div class="form-group"><label class="form-label">Phone Number</label><input class="form-input" type="tel" name="phone" value="<?= e($user['phone'] ?? '') ?>" placeholder="+1 234 567 890" /></div>
              </div>
              <div class="form-group"><label class="form-label">Address</label><input class="form-input" type="text" name="address" value="<?= e($user['address'] ?? '') ?>" placeholder="95 South Park Avenue, NYC" /></div>
              <div class="form-group"><label class="form-label">About Yourself</label><textarea class="form-textarea" name="about" placeholder="Tell us a bit about yourself..."><?= e($user['about'] ?? '') ?></textarea></div>
              <button type="submit" class="btn btn-primary" id="saveProfileBtn"><i class="fa-solid fa-floppy-disk"></i> Save Changes</button>
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
