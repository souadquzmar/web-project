<?php
// ============================================================
//  includes/header.php
//  Usage: include __DIR__ . '/includes/header.php';
//  Expects $page_title and optionally $nav_active
// ============================================================
if (!isset($nav_active))  $nav_active  = '';
if (!isset($page_title))  $page_title  = 'FindHouses';
if (!isset($extra_css))   $extra_css   = '';
if (!isset($dash_mode))   $dash_mode   = false;
$u = current_user();
$navbar_class = $dash_mode ? 'navbar-dash' : 'navbar-hero';
$brand_color  = $dash_mode ? '' : 'style="color:#fff !important"';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <script>if(localStorage.getItem('fh-theme')==='dark')document.documentElement.classList.add('dark-mode');</script>
  <title><?= e($page_title) ?> — FindHouses</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"/>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
  <link rel="stylesheet" href="css/global.css">
  <?= $extra_css ?>
</head>
<body<?= $dash_mode ? ' class="dash-body"' : '' ?>>

<nav class="navbar <?= $navbar_class ?>" id="navbar">
  <div class="navbar-inner">
    <a href="index.php" class="site-brand" <?= $brand_color ?>>Find<span class="dot">.</span>Houses</a>
    <div class="navbar-links">
      <a href="index.php"   class="nav-link<?= $nav_active==='home'    ? ' active':'' ?>">Home</a>
      <a href="listing.php" class="nav-link<?= $nav_active==='listing' ? ' active':'' ?>">Listing</a>
      <a href="about.php"   class="nav-link<?= $nav_active==='about'   ? ' active':'' ?>">About</a>
      <a href="blog.php"    class="nav-link<?= $nav_active==='blog'    ? ' active':'' ?>">Blog</a>
      <a href="contact.php" class="nav-link<?= $nav_active==='contact' ? ' active':'' ?>">Contact</a>
    </div>
    <div class="navbar-actions">
      <button class="theme-toggle" id="themeToggle" aria-label="Toggle dark mode"><i class="fa-solid fa-moon"></i></button>
      <?php if ($u): ?>
        <div class="nav-dropdown">
          <div class="navbar-user">
            <img src="<?= e(avatar_url($u['avatar'] ?? '', $u['id'] ?? 0)) ?>" class="navbar-avatar" alt="<?= e($u['first_name']) ?>"/>
            Hi, <?= e($u['first_name']) ?>
          </div>
          <div class="nav-dropdown-menu">
            <a class="nav-dropdown-item" href="dashboard.php"><i class="fa-solid fa-gauge fa-fw"></i> Dashboard</a>
            <a class="nav-dropdown-item" href="user-profile.php"><i class="fa-regular fa-user fa-fw"></i> Edit Profile</a>
            <?php if (is_agent_or_admin()): ?>
            <a class="nav-dropdown-item" href="my-listings.php"><i class="fa-solid fa-house fa-fw"></i> My Properties</a>
            <?php endif; ?>
            <a class="nav-dropdown-item" href="favorited-listings.php"><i class="fa-solid fa-heart fa-fw"></i> Favorites</a>
            <a class="nav-dropdown-item" href="change-password.php"><i class="fa-solid fa-lock fa-fw"></i> Change Password</a>
            <a class="nav-dropdown-item" href="actions/logout.php" style="color:var(--mainColor)"><i class="fa-solid fa-right-from-bracket fa-fw"></i> Log Out</a>
          </div>
        </div>
        <?php if (is_agent_or_admin()): ?>
        <button class="btn btn-primary btn-sm" data-modal="add-listing-modal">
          <i class="fa-solid fa-plus"></i> Add Listing
        </button>
        <?php endif; ?>
      <?php else: ?>
        <a href="#" class="nav-link" data-modal="signin-modal">Sign In</a>
        <div class="navbar-divider"></div>
        <button class="btn btn-primary btn-sm" data-modal="signin-modal">
          <i class="fa-solid fa-user"></i> Join Free
        </button>
      <?php endif; ?>
    </div>
    <button class="navbar-toggle" aria-label="Menu"><span></span><span></span><span></span></button>
  </div>
</nav>

<?php if ($u && is_agent_or_admin()): ?>
<!-- ── ADD LISTING MODAL ─────────────────────────────────── -->
<div class="modal-overlay" id="add-listing-modal">
  <div class="modal-box" style="max-width:900px">
    <div class="modal-header">
      <div class="modal-title">Add New Listing</div>
      <button class="modal-close" data-close><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="modal-body" style="padding:28px 32px">
      <form action="actions/add_property.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token'] ?? '') ?>">
        <div class="listing-section">
          <div class="listing-section-title">Property Description &amp; Price</div>
          <div class="form-group"><label class="form-label">Property Title</label>
            <input class="form-input" type="text" name="title" placeholder="e.g. Modern Family Villa in NYC" required/></div>
          <div class="form-group"><label class="form-label">Description</label>
            <textarea class="form-textarea" name="description" style="min-height:100px" placeholder="Describe your property..." required></textarea></div>
          <div class="form-row" style="grid-template-columns:repeat(3,1fr)">
            <div class="form-group"><label class="form-label">Status</label>
              <select class="form-select" name="status">
                <option value="For Sale">For Sale</option><option value="For Rent">For Rent</option>
              </select></div>
            <div class="form-group"><label class="form-label">Type</label>
              <select class="form-select" name="type">
                <option>House</option><option>Commercial</option><option>Apartment</option>
                <option>Lot</option><option>Garage</option><option>Villa</option>
              </select></div>
            <div class="form-group"><label class="form-label">Rooms</label>
              <select class="form-select" name="rooms">
                <option>1</option><option>2</option><option>3</option><option>4</option><option>5</option><option>6</option>
              </select></div>
          </div>
          <div class="form-row">
            <div class="form-group"><label class="form-label">Price (USD)</label>
              <input class="form-input" type="number" name="price" min="0" step="0.01" placeholder="350000" required/></div>
            <div class="form-group"><label class="form-label">Area (sqft)</label>
              <input class="form-input" type="number" name="area_sqft" min="0" placeholder="1200"/></div>
          </div>
        </div>
        <div class="listing-section">
          <div class="listing-section-title">Property Media</div>
          <div class="upload-area" onclick="document.getElementById('modal-file-input').click()">
            <input type="file" id="modal-file-input" name="images[]" multiple accept="image/*" style="display:none"/>
            <i class="fa-solid fa-cloud-arrow-up"></i>
            <p>Click to upload images</p>
            <span class="upload-hint">PNG, JPG supported</span>
          </div>
        </div>
        <div class="listing-section">
          <div class="listing-section-title">Location</div>
          <div class="form-row">
            <div class="form-group"><label class="form-label">Address</label>
              <input class="form-input" type="text" name="address" placeholder="95 South Park Avenue"/></div>
            <div class="form-group"><label class="form-label">City</label>
              <input class="form-input" type="text" name="city" placeholder="New York"/></div>
          </div>
          <div class="form-row">
            <div class="form-group"><label class="form-label">State</label>
              <input class="form-input" type="text" name="state" placeholder="NY"/></div>
            <div class="form-group"><label class="form-label">Country</label>
              <input class="form-input" type="text" name="country" placeholder="USA"/></div>
          </div>
          <div class="form-row">
            <div class="form-group"><label class="form-label">Latitude</label>
              <input class="form-input" type="text" name="latitude" placeholder="40.7128"/></div>
            <div class="form-group"><label class="form-label">Longitude</label>
              <input class="form-input" type="text" name="longitude" placeholder="-74.0060"/></div>
          </div>
        </div>
        <div class="listing-section">
          <div class="listing-section-title">Extra Information</div>
          <div class="form-row" style="grid-template-columns:repeat(3,1fr)">
            <div class="form-group"><label class="form-label">Property Age</label>
              <select class="form-select" name="property_age">
                <option value="">Select Age</option>
                <option>0–1 years</option><option>0–5 years</option><option>0–10 years</option>
                <option>0–20 years</option><option>50+ years</option>
              </select></div>
            <div class="form-group"><label class="form-label">Bedrooms</label>
              <select class="form-select" name="bedrooms">
                <option value="">Any</option><option>1</option><option>2</option><option>3</option><option>4</option><option>5</option><option>6</option>
              </select></div>
            <div class="form-group"><label class="form-label">Bathrooms</label>
              <select class="form-select" name="bathrooms">
                <option value="">Any</option><option>1</option><option>2</option><option>3</option><option>4</option><option>5</option>
              </select></div>
          </div>
        </div>
        <div class="listing-section">
          <div class="listing-section-title">Features</div>
          <div class="listing-features">
            <?php foreach (['Air Conditioning','Swimming Pool','Central Heating','Laundry Room','Gym','Alarm','Window Covering','WiFi','TV Cable','Dryer','Microwave','Washer','Refrigerator','Parking','Balcony','Outdoor Shower'] as $feat): ?>
            <label class="listing-feature-check">
              <input type="checkbox" name="features[]" value="<?= e($feat) ?>"/>
              <span><?= e($feat) ?></span>
            </label>
            <?php endforeach; ?>
          </div>
        </div>
        <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:20px;padding-top:16px;border-top:1px solid var(--greyMid)">
          <button type="button" class="btn btn-outline" data-close>Cancel</button>
          <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Save Listing</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php endif; ?>
<?php if (!$u): ?>
<!-- ── SIGN IN / REGISTER MODAL ─────────────────────────── -->
<div class="modal-overlay" id="signin-modal">
  <div class="modal-box">
    <div class="modal-header">
      <div class="modal-title">Welcome to <span>FindHouses</span></div>
      <button class="modal-close" data-close><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="modal-body">
      <?= render_flash() ?>
      <div class="modal-tabs">
        <button class="modal-tab active" data-target="tab-login">Login</button>
        <button class="modal-tab" data-target="tab-register">Register</button>
      </div>
      <!-- LOGIN -->
      <div class="modal-pane active" id="tab-login">
        <form action="actions/login.php" method="POST">
          <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token'] ?? '') ?>">
          <div class="form-group"><label class="form-label">Email or Username</label>
            <input class="form-input" type="text" name="login_id" placeholder="you@example.com" required/></div>
          <div class="form-group"><label class="form-label">Password</label>
            <input class="form-input" type="password" name="password" placeholder="••••••••" required/></div>
          <div class="form-check-row">
            <label class="form-check-label"><input type="checkbox" name="remember" style="accent-color:var(--mainColor)"> Remember me</label>
            <a href="#" id="showForgotPassword" style="font-size:12px;color:var(--mainColor)">Forgot password?</a>
          </div>
          <button class="btn btn-primary" style="width:100%;justify-content:center;margin-top:18px">Log In</button>
        </form>
        <!-- Forgot Password Form (hidden by default) -->
        <div id="forgotPasswordForm" style="display:none">
          <div style="text-align:center;margin-bottom:20px">
            <i class="fa-solid fa-envelope-open-text" style="font-size:36px;color:var(--mainColor);margin-bottom:12px"></i>
            <h4 style="font-size:16px;font-weight:700;margin-bottom:6px">Forgot Your Password?</h4>
            <p style="font-size:13px;color:var(--textLight)">Enter your email and we'll send you a reset link.</p>
          </div>
          <form action="actions/forgot_password.php" method="POST">
            <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token'] ?? '') ?>">
            <div class="form-group"><label class="form-label">Email Address</label>
              <input class="form-input" type="email" name="email" placeholder="you@example.com" required /></div>
            <button class="btn btn-primary" type="submit" style="width:100%;justify-content:center"><i class="fa-solid fa-paper-plane"></i> Send Reset Link</button>
          </form>
          <div style="text-align:center;margin-top:16px">
            <a href="#" id="backToLogin" style="font-size:13px;color:var(--mainColor)"><i class="fa-solid fa-arrow-left"></i> Back to Login</a>
          </div>
        </div>
      </div>
      <!-- REGISTER -->
      <div class="modal-pane" id="tab-register">
        <form action="actions/register.php" method="POST">
          <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token'] ?? '') ?>">
          <div class="form-row">
            <div class="form-group"><label class="form-label">First Name</label>
              <input class="form-input" type="text" name="first_name" placeholder="John" required/></div>
            <div class="form-group"><label class="form-label">Last Name</label>
              <input class="form-input" type="text" name="last_name" placeholder="Smith" required/></div>
          </div>
          <div class="form-group"><label class="form-label">Email</label>
            <input class="form-input" type="email" name="email" placeholder="you@example.com" required/></div>
          <div class="form-group"><label class="form-label">Password</label>
            <input class="form-input" type="password" name="password" placeholder="Min. 8 characters" required minlength="8"/></div>
          <button class="btn btn-primary" style="width:100%;justify-content:center;margin-top:4px">Create Account</button>
        </form>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>

<!-- Mobile Drawer -->
<div class="mobile-overlay" id="mobileOverlay"></div>
<aside class="mobile-drawer" id="mobileDrawer">
  <div class="mobile-drawer__header">
    <span class="site-brand" style="color:var(--dark)!important">Find<span class="dot">.</span>Houses</span>
    <button class="mobile-drawer__close" id="mobileClose" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
  </div>
  <nav class="mobile-drawer__nav">
    <a href="index.php"   class="mobile-nav-link">Home</a>
    <a href="listing.php" class="mobile-nav-link">Listing</a>
    <a href="about.php"   class="mobile-nav-link">About Us</a>
    <a href="blog.php"    class="mobile-nav-link">Blog</a>
    <a href="contact.php" class="mobile-nav-link">Contact</a>
  </nav>
</aside>
